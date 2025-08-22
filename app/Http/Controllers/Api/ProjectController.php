<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::published();

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter featured projects
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereFullText(['title', 'short_description', 'description'], $search);
        }

        // Filter by technology
        if ($request->filled('technology')) {
            $query->whereJsonContains('technologies', $request->technology);
        }

        $perPage = min($request->get('per_page', 12), 50);
        $projects = $query->ordered()->paginate($perPage);

        return ProjectResource::collection($projects);
    }

    public function show(Project $project)
    {
        if (!$project->is_published) {
            abort(404);
        }

        // Increment view count
        $project->incrementViewCount();

        return new ProjectResource($project);
    }

    public function featured()
    {
        $projects = Project::published()
            ->featured()
            ->ordered()
            ->limit(config('portfolio.content.featured_projects_count', 6))
            ->get();

        return ProjectResource::collection($projects);
    }

    public function technologies()
    {
        $technologies = Project::published()
            ->whereNotNull('technologies')
            ->get()
            ->pluck('technologies')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return response()->json([
            'data' => $technologies
        ]);
    }

    public function statuses()
    {
        $statuses = [
            'planning' => 'Planning',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'maintenance' => 'Maintenance'
        ];

        return response()->json([
            'data' => $statuses
        ]);
    }
}
