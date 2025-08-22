<?php

namespace App\Services;

use App\Models\PageView;
use App\Models\Project;
use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function trackPageView($url, $userAgent = null, $ipAddress = null, $referrer = null)
    {
        PageView::create([
            'url' => $url,
            'user_agent' => $userAgent ?: request()->userAgent(),
            'ip_address' => $ipAddress ?: request()->ip(),
            'referrer' => $referrer ?: request()->header('referer'),
            'visited_at' => now()
        ]);
    }

    public function getVisitorStats($startDate)
    {
        return [
            'total_visitors' => PageView::where('visited_at', '>=', $startDate)
                ->distinct('ip_address')->count(),
            'total_pageviews' => PageView::where('visited_at', '>=', $startDate)->count(),
            'unique_pages' => PageView::where('visited_at', '>=', $startDate)
                ->distinct('url')->count(),
            'avg_session_duration' => $this->calculateAverageSessionDuration($startDate)
        ];
    }

    public function getEngagementStats($startDate)
    {
        return [
            'bounce_rate' => $this->calculateBounceRate($startDate),
            'pages_per_session' => $this->calculatePagesPerSession($startDate),
            'top_pages' => $this->getTopPages($startDate),
            'exit_pages' => $this->getExitPages($startDate)
        ];
    }

    public function getPopularContent($startDate)
    {
        return [
            'projects' => Project::withCount(['views' => function($query) use ($startDate) {
                $query->where('visited_at', '>=', $startDate);
            }])->orderBy('views_count', 'desc')->limit(5)->get(),
            'blog_posts' => BlogPost::withCount(['views' => function($query) use ($startDate) {
                $query->where('visited_at', '>=', $startDate);
            }])->orderBy('views_count', 'desc')->limit(5)->get()
        ];
    }

    public function getTrafficSources($startDate)
    {
        return PageView::where('visited_at', '>=', $startDate)
            ->whereNotNull('referrer')
            ->selectRaw('
                CASE 
                    WHEN referrer LIKE "%google%" THEN "Google"
                    WHEN referrer LIKE "%github%" THEN "GitHub"
                    WHEN referrer LIKE "%linkedin%" THEN "LinkedIn"
                    WHEN referrer LIKE "%twitter%" THEN "Twitter"
                    ELSE "Other"
                END as source,
                COUNT(*) as visits
            ')
            ->groupBy('source')
            ->orderBy('visits', 'desc')
            ->pluck('visits', 'source');
    }

    public function getDeviceStats($startDate)
    {
        return PageView::where('visited_at', '>=', $startDate)
            ->selectRaw('
                CASE 
                    WHEN user_agent LIKE "%Mobile%" THEN "Mobile"
                    WHEN user_agent LIKE "%Tablet%" THEN "Tablet"
                    ELSE "Desktop"
                END as device_type,
                COUNT(*) as visits
            ')
            ->groupBy('device_type')
            ->pluck('visits', 'device_type');
    }

    public function getVisitorChartData($startDate)
    {
        return PageView::where('visited_at', '>=', $startDate)
            ->selectRaw('DATE(visited_at) as date, COUNT(DISTINCT ip_address) as visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'visitors' => $item->visitors
                ];
            });
    }

    public function getPageviewChartData($startDate)
    {
        return PageView::where('visited_at', '>=', $startDate)
            ->selectRaw('DATE(visited_at) as date, COUNT(*) as pageviews')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'pageviews' => $item->pageviews
                ];
            });
    }

    private function calculateAverageSessionDuration($startDate)
    {
        // Simplified calculation - in production, you'd want more sophisticated session tracking
        return PageView::where('visited_at', '>=', $startDate)
            ->selectRaw('ip_address, MAX(visited_at) - MIN(visited_at) as duration')
            ->groupBy('ip_address')
            ->having('duration', '>', 0)
            ->avg('duration') ?? 0;
    }

    private function calculateBounceRate($startDate)
    {
        $totalSessions = PageView::where('visited_at', '>=', $startDate)
            ->distinct('ip_address')->count();
        
        $bounceSessions = PageView::where('visited_at', '>=', $startDate)
            ->selectRaw('ip_address, COUNT(*) as page_count')
            ->groupBy('ip_address')
            ->having('page_count', '=', 1)
            ->count();

        return $totalSessions > 0 ? round(($bounceSessions / $totalSessions) * 100, 2) : 0;
    }

    private function calculatePagesPerSession($startDate)
    {
        $totalPages = PageView::where('visited_at', '>=', $startDate)->count();
        $totalSessions = PageView::where('visited_at', '>=', $startDate)
            ->distinct('ip_address')->count();

        return $totalSessions > 0 ? round($totalPages / $totalSessions, 2) : 0;
    }

    private function getTopPages($startDate)
    {
        return PageView::where('visited_at', '>=', $startDate)
            ->selectRaw('url, COUNT(*) as views')
            ->groupBy('url')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->pluck('views', 'url');
    }

    private function getExitPages($startDate)
    {
        return PageView::where('visited_at', '>=', $startDate)
            ->selectRaw('url, COUNT(*) as exits')
            ->groupBy('url')
            ->orderBy('exits', 'desc')
            ->limit(10)
            ->pluck('exits', 'url');
    }
}
