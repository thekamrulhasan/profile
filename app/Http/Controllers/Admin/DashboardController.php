<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BlogPost;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super_admin,admin,editor');
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_projects' => Project::count(),
            'published_projects' => Project::where('is_published', true)->count(),
            'total_skills' => Skill::count(),
            'featured_skills' => Skill::where('is_featured', true)->count(),
            'total_experiences' => Experience::count(),
            'current_experiences' => Experience::where('is_current', true)->count(),
            'total_blog_posts' => BlogPost::count(),
            'published_blog_posts' => BlogPost::where('status', 'published')->count(),
        ];

        // Recent activities from audit logs
        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // User registration chart data (last 30 days)
        $userRegistrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Project status distribution
        $projectStatuses = Project::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Skills by category
        $skillsByCategory = Skill::select('category', DB::raw('COUNT(*) as count'))
            ->where('is_active', true)
            ->groupBy('category')
            ->get();

        // Most viewed projects
        $popularProjects = Project::where('is_published', true)
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get(['title', 'view_count', 'slug']);

        // Most viewed blog posts
        $popularPosts = BlogPost::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get(['title', 'view_count', 'slug']);

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'userRegistrations',
            'projectStatuses',
            'skillsByCategory',
            'popularProjects',
            'popularPosts'
        ));
    }

    public function analytics()
    {
        $this->middleware('permission:analytics.view');

        // Monthly user registrations (last 12 months)
        $monthlyRegistrations = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Content creation over time
        $contentCreation = [
            'projects' => Project::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get(),

            'blog_posts' => BlogPost::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get(),
        ];

        // User activity by role
        $usersByRole = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->select('roles.display_name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.id', 'roles.display_name')
            ->get();

        return view('admin.analytics', compact(
            'monthlyRegistrations',
            'contentCreation',
            'usersByRole'
        ));
    }
}
