<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    public function index(): View
    {
        $this->authorize('manage', Category::class);

        $categories = Category::query()->withCount('posts')->orderBy('name')->get();
        $tags = Tag::query()->withCount('posts')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'tags'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->createCategory($request->validated('name'));

        return back()->with('success', 'Category created.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function destroyTag(Tag $tag): RedirectResponse
    {
        $this->authorize('manage', Category::class);

        $tag->delete();

        return back()->with('success', 'Tag deleted.');
    }
}
