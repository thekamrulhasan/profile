<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $query = Experience::active();

        // Filter current positions
        if ($request->boolean('current')) {
            $query->current();
        }

        // Search by company or position
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $experiences = $query->ordered()->get();

        return ExperienceResource::collection($experiences);
    }

    public function show(Experience $experience)
    {
        if (!$experience->is_active) {
            abort(404);
        }

        return new ExperienceResource($experience);
    }

    public function current()
    {
        $experiences = Experience::active()
            ->current()
            ->ordered()
            ->get();

        return ExperienceResource::collection($experiences);
    }

    public function timeline()
    {
        $experiences = Experience::active()
            ->ordered()
            ->get()
            ->map(function ($experience) {
                return [
                    'id' => $experience->id,
                    'company_name' => $experience->company_name,
                    'position' => $experience->position,
                    'start_date' => $experience->start_date?->toDateString(),
                    'end_date' => $experience->end_date?->toDateString(),
                    'is_current' => $experience->is_current,
                    'duration' => $experience->duration,
                    'formatted_date_range' => $experience->formatted_date_range,
                    'technologies' => $experience->technologies ?? [],
                ];
            });

        return response()->json([
            'data' => $experiences
        ]);
    }
}
