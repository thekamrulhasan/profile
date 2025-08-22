<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:content.view')->only(['index', 'show']);
        $this->middleware('permission:content.create')->only(['create', 'store']);
        $this->middleware('permission:content.edit')->only(['edit', 'update']);
        $this->middleware('permission:content.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Project::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Published filter
        if ($request->filled('published')) {
            $query->where('is_published', $request->published === 'published');
        }

        $projects = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects'],
            'short_description' => ['required', 'string'],
            'description' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'gallery_images.*' => ['nullable', 'image', 'max:2048'],
            'technologies' => ['nullable', 'string'],
            'project_url' => ['nullable', 'url'],
            'github_url' => ['nullable', 'url'],
            'demo_url' => ['nullable', 'url'],
            'status' => ['required', 'in:planning,in_progress,completed,maintenance'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data = $request->all();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('projects', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryImages = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryImages[] = $image->store('projects/gallery', 'public');
            }
            $data['gallery_images'] = $galleryImages;
        }

        // Parse technologies
        if ($request->filled('technologies')) {
            $data['technologies'] = array_map('trim', explode(',', $request->technologies));
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $project = Project::create($data);

        // Log project creation
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => Project::class,
            'model_id' => $project->id,
            'new_values' => $project->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.projects.index')
                        ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug,' . $project->id],
            'short_description' => ['required', 'string'],
            'description' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'gallery_images.*' => ['nullable', 'image', 'max:2048'],
            'technologies' => ['nullable', 'string'],
            'project_url' => ['nullable', 'url'],
            'github_url' => ['nullable', 'url'],
            'demo_url' => ['nullable', 'url'],
            'status' => ['required', 'in:planning,in_progress,completed,maintenance'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $oldValues = $project->toArray();
        $data = $request->all();

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('projects', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images
            if ($project->gallery_images) {
                foreach ($project->gallery_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            $galleryImages = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryImages[] = $image->store('projects/gallery', 'public');
            }
            $data['gallery_images'] = $galleryImages;
        }

        // Parse technologies
        if ($request->filled('technologies')) {
            $data['technologies'] = array_map('trim', explode(',', $request->technologies));
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $project->update($data);

        // Log project update
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => Project::class,
            'model_id' => $project->id,
            'old_values' => $oldValues,
            'new_values' => $project->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.projects.index')
                        ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $oldValues = $project->toArray();

        // Delete associated images
        if ($project->featured_image) {
            Storage::disk('public')->delete($project->featured_image);
        }

        if ($project->gallery_images) {
            foreach ($project->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $project->delete();

        // Log project deletion
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model_type' => Project::class,
            'model_id' => $project->id,
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.projects.index')
                        ->with('success', 'Project deleted successfully.');
    }
}
