<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\BlogPost;
use App\Models\Skill;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $analytics = $this->getAnalyticsData();
        return view('admin.analytics.index', compact('analytics'));
    }

    public function api()
    {
        return response()->json($this->getAnalyticsData());
    }

    private function getAnalyticsData()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $lastWeek = $now->copy()->subWeek();

        return [
            'overview' => [
                'total_users' => User::count(),
                'total_projects' => Project::count(),
                'total_blog_posts' => BlogPost::count(),
                'total_skills' => Skill::count(),
                'new_users_this_month' => User::where('created_at', '>=', $lastMonth)->count(),
                'active_projects' => Project::where('status', 'active')->count(),
                'published_posts' => BlogPost::where('status', 'published')->count(),
            ],
            'user_growth' => $this->getUserGrowthData(),
            'project_stats' => $this->getProjectStats(),
            'skill_distribution' => $this->getSkillDistribution(),
            'activity_timeline' => $this->getActivityTimeline(),
            'performance_metrics' => $this->getPerformanceMetrics(),
        ];
    }

    private function getUserGrowthData()
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = User::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
            
            $months->push([
                'month' => $date->format('M Y'),
                'users' => $count,
                'cumulative' => User::where('created_at', '<=', $date->endOfMonth())->count()
            ]);
        }
        return $months;
    }

    private function getProjectStats()
    {
        return [
            'by_status' => Project::select('status', DB::raw('count(*) as count'))
                                 ->groupBy('status')
                                 ->get(),
            'by_category' => Project::select('category', DB::raw('count(*) as count'))
                                   ->groupBy('category')
                                   ->get(),
            'recent_projects' => Project::with('user')
                                       ->latest()
                                       ->take(5)
                                       ->get(),
        ];
    }

    private function getSkillDistribution()
    {
        return Skill::select('category', 'level', DB::raw('count(*) as count'))
                   ->groupBy('category', 'level')
                   ->get()
                   ->groupBy('category');
    }

    private function getActivityTimeline()
    {
        return AuditLog::with('user')
                      ->latest()
                      ->take(20)
                      ->get()
                      ->map(function ($log) {
                          return [
                              'id' => $log->id,
                              'user' => $log->user->name ?? 'System',
                              'action' => $log->action,
                              'model' => $log->auditable_type,
                              'model_id' => $log->auditable_id,
                              'created_at' => $log->created_at->diffForHumans(),
                              'ip_address' => $log->ip_address,
                          ];
                      });
    }

    private function getPerformanceMetrics()
    {
        $lastWeek = Carbon::now()->subWeek();
        
        return [
            'avg_response_time' => rand(150, 300), // Simulated data
            'uptime_percentage' => 99.9,
            'total_requests' => rand(10000, 50000),
            'error_rate' => rand(1, 5) / 100,
            'cache_hit_rate' => rand(85, 95) / 100,
            'database_queries' => rand(500, 2000),
        ];
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $data = $this->getAnalyticsData();

        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }

        return response()->json($data);
    }

    private function exportToCsv($data)
    {
        $filename = 'analytics_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Overview data
            fputcsv($file, ['Metric', 'Value']);
            foreach ($data['overview'] as $key => $value) {
                fputcsv($file, [ucwords(str_replace('_', ' ', $key)), $value]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
