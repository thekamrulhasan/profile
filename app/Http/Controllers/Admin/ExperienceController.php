<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Http\Requests\Admin\StoreExperienceRequest;
use App\Http\Requests\Admin\UpdateExperienceRequest;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,editor']);
    }

    public function index(Request $request)
    {
        $query = Experience::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('company', 'like', '%' . $request->search . '%')
                  ->orWhere('position', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $experiences = $query->orderBy('start_date', 'desc')->paginate(10);

        return view('admin.experiences.index', compact('experiences'));
    }

    public function create()
    {
        return view('admin.experiences.create');
    }

    public function store(StoreExperienceRequest $request)
    {
        Experience::create($request->validated());

        return redirect()->route('admin.experiences.index')
            ->with('success', 'Experience created successfully.');
    }

    public function show(Experience $experience)
    {
        return view('admin.experiences.show', compact('experience'));
    }

    public function edit(Experience $experience)
    {
        return view('admin.experiences.edit', compact('experience'));
    }

    public function update(UpdateExperienceRequest $request, Experience $experience)
    {
        $experience->update($request->validated());

        return redirect()->route('admin.experiences.index')
            ->with('success', 'Experience updated successfully.');
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return redirect()->route('admin.experiences.index')
            ->with('success', 'Experience deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'selected' => 'required|array|min:1',
            'selected.*' => 'exists:experiences,id'
        ]);

        switch ($request->action) {
            case 'delete':
                $this->authorize('deleteAny', Experience::class); // Authorize bulk delete
                Experience::whereIn('id', $request->selected)->delete();
                $message = 'Selected experiences deleted successfully.';
                break;
            case 'activate':
                $this->authorize('updateAny', Experience::class); // Authorize bulk update
                Experience::whereIn('id', $request->selected)->update(['is_active' => true]);
                $message = 'Selected experiences activated successfully.';
                break;
            case 'deactivate':
                $this->authorize('updateAny', Experience::class); // Authorize bulk update
                Experience::whereIn('id', $request->selected)->update(['is_active' => false]);
                $message = 'Selected experiences deactivated successfully.';
                break;
        }

        return redirect()->route('admin.experiences.index')->with('success', $message);
    }
}
