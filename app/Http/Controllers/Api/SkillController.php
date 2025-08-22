<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        $query = Skill::active();

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter featured skills
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $skills = $query->ordered()->get();

        return SkillResource::collection($skills);
    }

    public function show(Skill $skill)
    {
        if (!$skill->is_active) {
            abort(404);
        }

        return new SkillResource($skill);
    }

    public function categories()
    {
        $categories = Skill::active()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json([
            'data' => $categories
        ]);
    }

    public function featured()
    {
        $skills = Skill::active()
            ->featured()
            ->ordered()
            ->limit(config('portfolio.content.featured_skills_count', 8))
            ->get();

        return SkillResource::collection($skills);
    }
}
