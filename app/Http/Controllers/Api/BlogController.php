<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with('user')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        // Search by title, excerpt, or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereFullText(['title', 'excerpt', 'content'], $search);
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        // Filter by author
        if ($request->filled('author')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->author . '%');
            });
        }

        $perPage = min($request->get('per_page', 10), 50);
        $posts = $query->orderBy('published_at', 'desc')->paginate($perPage);

        return BlogPostResource::collection($posts);
    }

    public function show(BlogPost $blogPost)
    {
        if ($blogPost->status !== 'published' || 
            !$blogPost->published_at || 
            $blogPost->published_at->isFuture()) {
            abort(404);
        }

        $blogPost->load('user');
        
        // Increment view count
        $blogPost->increment('view_count');

        return new BlogPostResource($blogPost);
    }

    public function recent()
    {
        $posts = BlogPost::with('user')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(config('portfolio.content.recent_posts_count', 5))
            ->get();

        return BlogPostResource::collection($posts);
    }

    public function tags()
    {
        $tags = BlogPost::where('status', 'published')
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return response()->json([
            'data' => $tags
        ]);
    }

    public function archive()
    {
        $archive = BlogPost::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'month' => $item->month,
                    'month_name' => date('F', mktime(0, 0, 0, $item->month, 1)),
                    'count' => $item->count,
                ];
            });

        return response()->json([
            'data' => $archive
        ]);
    }
}
