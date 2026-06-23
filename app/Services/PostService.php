<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(
        private readonly ContentSanitizer $sanitizer,
        private readonly PostCacheService $cacheService,
    ) {}

    public function listPublishedPosts(
        ?string $search = null,
        ?int $categoryId = null,
        ?string $tagSlug = null,
        int $perPage = 9,
        int $page = 1,
    ): LengthAwarePaginator {
        $search = $search !== null ? trim($search) : '';
        $tagSlug = $tagSlug !== null ? trim($tagSlug) : '';
        $hasFilters = $search !== '' || $categoryId !== null || $tagSlug !== '';

        if ($hasFilters) {
            return $this->buildPublishedQuery($search, $categoryId, $tagSlug)
                ->paginate($perPage, ['*'], 'page', $page)
                ->withQueryString();
        }

        $cached = $this->cacheService->remember(
            "published.list.p{$page}.{$perPage}",
            fn () => $this->fetchPublishedListMeta($perPage, $page),
        );

        return $this->paginatorFromCachedMeta($cached);
    }

    /**
     * @return array{total: int, ids: list<int>, per_page: int, current_page: int}
     */
    private function fetchPublishedListMeta(int $perPage, int $page): array
    {
        $query = $this->buildPublishedBaseQuery(null, null, null);

        return [
            'total' => $query->count(),
            'ids' => (clone $query)->forPage($page, $perPage)->pluck('id')->all(),
            'per_page' => $perPage,
            'current_page' => $page,
        ];
    }

    /**
     * @param  array{total: int, ids: list<int>, per_page: int, current_page: int}  $meta
     */
    private function paginatorFromCachedMeta(array $meta): LengthAwarePaginator
    {
        if ($meta['ids'] === []) {
            return new Paginator(
                collect(),
                $meta['total'],
                $meta['per_page'],
                $meta['current_page'],
                ['path' => request()->url(), 'query' => request()->query()],
            );
        }

        $order = array_flip($meta['ids']);

        $items = Post::query()
            ->with(['user', 'category', 'tags'])
            ->withCount('approvedComments')
            ->whereIn('id', $meta['ids'])
            ->get()
            ->sortBy(fn (Post $post) => $order[$post->id])
            ->values();

        return new Paginator(
            $items,
            $meta['total'],
            $meta['per_page'],
            $meta['current_page'],
            ['path' => request()->url(), 'query' => request()->query()],
        );
    }

    private function buildPublishedBaseQuery(?string $search, ?int $categoryId, ?string $tagSlug)
    {
        return Post::query()
            ->where('status', 'published')
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                $term = '%'.strtolower($search).'%';
                $query->where(function ($builder) use ($term) {
                    $builder->whereRaw('LOWER(title) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(content) LIKE ?', [$term]);
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($tagSlug !== null && $tagSlug !== '', function ($query) use ($tagSlug) {
                $query->whereHas('tags', fn ($tagQuery) => $tagQuery->where('slug', $tagSlug));
            })
            ->latest();
    }

    private function buildPublishedQuery(?string $search, ?int $categoryId, ?string $tagSlug)
    {
        return $this->buildPublishedBaseQuery($search, $categoryId, $tagSlug)
            ->with(['user', 'category', 'tags'])
            ->withCount('approvedComments');
    }

    public function createPost(array $data): Post
    {
        $post = DB::transaction(function () use ($data) {
            $featuredImagePath = $this->storeFeaturedImage($data['featured_image'] ?? null);

            $post = Post::create([
                'user_id' => auth()->id(),
                'category_id' => $data['category_id'] ?? null,
                'title' => $data['title'],
                'slug' => $this->generateUniqueSlug($data['title']),
                'content' => $this->sanitizer->sanitize($data['content']),
                'featured_image' => $featuredImagePath,
                'status' => $data['status'],
                'published_at' => $data['status'] === 'published' ? now() : null,
            ]);

            $this->syncTags($post, $data['tags'] ?? null);

            return $post;
        });

        $this->cacheService->clear();

        return $post;
    }

    public function updatePost(Post $post, array $data): Post
    {
        $post = DB::transaction(function () use ($post, $data) {
            $attributes = [
                'category_id' => $data['category_id'] ?? null,
                'title' => $data['title'],
                'content' => $this->sanitizer->sanitize($data['content']),
                'status' => $data['status'],
                'published_at' => $data['status'] === 'published' ? ($post->published_at ?? now()) : null,
            ];

            if ($post->title !== $data['title']) {
                $attributes['slug'] = $this->generateUniqueSlug($data['title'], $post->id);
            }

            if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
                $this->deleteFeaturedImage($post->featured_image);
                $attributes['featured_image'] = $this->storeFeaturedImage($data['featured_image']);
            }

            $post->update($attributes);
            $this->syncTags($post, $data['tags'] ?? null);

            return $post->refresh();
        });

        $this->cacheService->clear();

        return $post;
    }

    public function deletePost(Post $post): void
    {
        DB::transaction(function () use ($post) {
            $this->deleteFeaturedImage($post->featured_image);
            $post->delete();
        });

        $this->cacheService->clear();
    }

    public function incrementViewCount(Post $post): Post
    {
        $post->increment('view_count');

        return $post->refresh();
    }

    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = Post::withTrashed()->where('slug', $slug);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    private function storeFeaturedImage(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        return $file->store('featured-images', 'public');
    }

    private function deleteFeaturedImage(?string $path): void
    {
        if ($path !== null && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function syncTags(Post $post, ?string $tagsInput): void
    {
        if ($tagsInput === null) {
            return;
        }

        $tagNames = collect(explode(',', $tagsInput))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->values();

        if ($tagNames->isEmpty()) {
            $post->tags()->detach();

            return;
        }

        $tagIds = $tagNames->map(function (string $name) {
            $slug = Str::slug($name);

            return Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            )->id;
        });

        $post->tags()->sync($tagIds);
    }
}
