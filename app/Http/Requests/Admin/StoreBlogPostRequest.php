<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole(['admin', 'editor']);
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_featured' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Post title is required.',
            'content.required' => 'Post content is required.',
            'featured_image.image' => 'Featured image must be a valid image file.',
            'featured_image.max' => 'Featured image size cannot exceed 2MB.',
            'status.required' => 'Post status is required.',
            'status.in' => 'Post status must be either draft or published.'
        ];
    }
}
