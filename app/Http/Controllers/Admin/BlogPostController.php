<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Http\Requests\Admin\StoreBlogPostRequest;
use App\Http\Requests\Admin\UpdateBlogPostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,editor']);
    }

    public function index(Request $request)
    {
        $query = BlogPost::with('author');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = BlogPost::distinct()->pluck('category')->filter();

        return view('admin.blog.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = BlogPost::distinct()->pluck('category')->filter();
        return view('admin.blog.create', compact('categories'));
    }

    public function store(StoreBlogPostRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post created successfully.');
    }

    public function show(BlogPost $blogPost)
    {
        return view('admin.blog.show', compact('blogPost'));
    }

    public function edit(BlogPost $blogPost)
    {
        $categories = BlogPost::distinct()->pluck('category')->filter();
        return view('admin.blog.edit', compact('blogPost', 'categories'));
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $blogPost->update($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blogPost)
    {
        if ($blogPost->featured_image) {
            \Storage::disk('public')->delete($blogPost->featured_image);
        }
        
        $blogPost->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post deleted successfully.');
    }
}
