<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('post'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
            'status' => ['required', 'in:draft,published'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a post title.',
            'title.min' => 'Title must be at least 3 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'content.required' => 'Please enter post content.',
            'content.min' => 'Content must be at least 10 characters.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Status must be Draft or Published.',
            'featured_image.image' => 'Featured image must be a valid image file.',
            'featured_image.mimes' => 'Featured image must be JPG, JPEG, PNG, or WEBP.',
            'featured_image.max' => 'Featured image must not exceed 2MB.',
        ];
    }
}
