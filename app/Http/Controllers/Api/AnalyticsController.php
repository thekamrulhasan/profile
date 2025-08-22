<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
        $this->middleware(['auth:sanctum', 'role:admin,super_admin']);
    }

    public function overview(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);

        return response()->json([
            'visitor_stats' => $this->analyticsService->getVisitorStats($startDate),
            'engagement_stats' => $this->analyticsService->getEngagementStats($startDate),
            'popular_content' => $this->analyticsService->getPopularContent($startDate),
            'traffic_sources' => $this->analyticsService->getTrafficSources($startDate),
            'device_stats' => $this->analyticsService->getDeviceStats($startDate),
        ]);
    }

    public function visitors(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);

        return response()->json([
            'chart_data' => $this->analyticsService->getVisitorChartData($startDate),
            'stats' => $this->analyticsService->getVisitorStats($startDate),
        ]);
    }

    public function pageviews(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);

        return response()->json([
            'chart_data' => $this->analyticsService->getPageviewChartData($startDate),
            'stats' => $this->analyticsService->getVisitorStats($startDate),
        ]);
    }

    public function realtime()
    {
        $last5Minutes = Carbon::now()->subMinutes(5);

        return response()->json([
            'active_visitors' => $this->analyticsService->getVisitorStats($last5Minutes)['total_visitors'],
            'recent_pageviews' => $this->analyticsService->getPageviewChartData($last5Minutes),
            'top_pages' => $this->analyticsService->getEngagementStats($last5Minutes)['top_pages'],
        ]);
    }
}
