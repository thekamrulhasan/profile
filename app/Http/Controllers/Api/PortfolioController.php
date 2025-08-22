<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SkillResource;
use App\Models\BlogPost;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;

class PortfolioController extends Controller
{
    public function overview()
    {
        $data = [
            'owner' => config('portfolio.owner'),
            'social' => config('portfolio.social'),
            'stats' => [
                'total_projects' => Project::published()->count(),
                'completed_projects' => Project::published()->byStatus('completed')->count(),
                'years_experience' => $this->calculateYearsOfExperience(),
                'technologies' => Skill::active()->count(),
                'blog_posts' => BlogPost::where('status', 'published')->count(),
            ],
            'featured_skills' => SkillResource::collection(
                Skill::active()->featured()->ordered()->limit(8)->get()
            ),
            'featured_projects' => ProjectResource::collection(
                Project::published()->featured()->ordered()->limit(6)->get()
            ),
            'current_experience' => ExperienceResource::collection(
                Experience::active()->current()->ordered()->get()
            ),
            'recent_posts' => BlogPostResource::collection(
                BlogPost::with('user')
                    ->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->orderBy('published_at', 'desc')
                    ->limit(3)
                    ->get()
            ),
        ];

        return response()->json([
            'data' => $data
        ]);
    }

    public function contact()
    {
        $data = [
            'owner' => config('portfolio.owner'),
            'social' => config('portfolio.social'),
            'contact_form_enabled' => true,
        ];

        return response()->json([
            'data' => $data
        ]);
    }

    public function seo()
    {
        $data = [
            'title' => config('portfolio.seo.default_title'),
            'description' => config('portfolio.seo.default_description'),
            'keywords' => config('portfolio.seo.default_keywords'),
            'image' => asset(config('portfolio.seo.default_image')),
            'url' => config('app.url'),
            'type' => 'website',
            'author' => config('portfolio.owner.name'),
        ];

        return response()->json([
            'data' => $data
        ]);
    }

    private function calculateYearsOfExperience(): int
    {
        $firstExperience = Experience::active()
            ->orderBy('start_date')
            ->first();

        if (!$firstExperience) {
            return 0;
        }

        return $firstExperience->start_date->diffInYears(now());
    }
}
