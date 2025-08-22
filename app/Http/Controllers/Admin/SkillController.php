<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
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
        $query = Skill::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $skills = $query->orderBy('sort_order')->orderBy('name')->paginate(15);
        $categories = Skill::distinct()->pluck('category');

        return view('admin.skills.index', compact('skills', 'categories'));
    }

    public function create()
    {
        $categories = Skill::distinct()->pluck('category');
        return view('admin.skills.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'proficiency_level' => ['required', 'integer', 'min:0', 'max:100'],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $skill = Skill::create([
            'name' => $request->name,
            'category' => $request->category,
            'proficiency_level' => $request->proficiency_level,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Log skill creation
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => Skill::class,
            'model_id' => $skill->id,
            'new_values' => $skill->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.skills.index')
                        ->with('success', 'Skill created successfully.');
    }

    public function show(Skill $skill)
    {
        return view('admin.skills.show', compact('skill'));
    }

    public function edit(Skill $skill)
    {
        $categories = Skill::distinct()->pluck('category');
        return view('admin.skills.edit', compact('skill', 'categories'));
    }

    public function update(Request $request, Skill $skill)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'proficiency_level' => ['required', 'integer', 'min:0', 'max:100'],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $skill->toArray();

        $skill->update([
            'name' => $request->name,
            'category' => $request->category,
            'proficiency_level' => $request->proficiency_level,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Log skill update
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => Skill::class,
            'model_id' => $skill->id,
            'old_values' => $oldValues,
            'new_values' => $skill->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.skills.index')
                        ->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        $oldValues = $skill->toArray();

        $skill->delete();

        // Log skill deletion
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model_type' => Skill::class,
            'model_id' => $skill->id,
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.skills.index')
                        ->with('success', 'Skill deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:delete,activate,deactivate,feature,unfeature'],
            'skills' => ['required', 'array'],
            'skills.*' => ['exists:skills,id'],
        ]);

        switch ($request->action) {
            case 'delete':
                $this->authorize('deleteAny', Skill::class); // Authorize bulk delete
                Skill::whereIn('id', $request->skills)->delete();
                $message = 'Selected skills deleted successfully.';
                break;
            case 'activate':
            case 'deactivate':
            case 'feature':
            case 'unfeature':
                $this->authorize('updateAny', Skill::class); // Authorize bulk update
                $updateData = [];
                if ($request->action === 'activate') {
                    $updateData['is_active'] = true;
                } elseif ($request->action === 'deactivate') {
                    $updateData['is_active'] = false;
                } elseif ($request->action === 'feature') {
                    $updateData['is_featured'] = true;
                } elseif ($request->action === 'unfeature') {
                    $updateData['is_featured'] = false;
                }
                Skill::whereIn('id', $request->skills)->update($updateData);
                $message = ucfirst($request->action) . ' action completed for ' . count($request->skills) . ' skills.';
                break;
        }

        // Log bulk action for each skill
        foreach ($request->skills as $skillId) {
            $skill = Skill::find($skillId);
            if ($skill) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => $request->action,
                    'model_type' => Skill::class,
                    'model_id' => $skill->id,
                    'old_values' => $skill->getOriginal(), // Get original values before update
                    'new_values' => $skill->fresh()->toArray(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return redirect()->route('admin.skills.index')->with('success', $message);
    }
}
