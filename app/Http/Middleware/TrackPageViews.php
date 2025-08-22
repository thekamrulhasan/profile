<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;

class TrackPageViews
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track GET requests that return successful responses
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // Don't track admin routes, API routes, or asset requests
            if (!$request->is('admin/*') && 
                !$request->is('api/*') && 
                !$request->is('_next/*') &&
                !str_contains($request->path(), '.')) {
                
                $this->analyticsService->trackPageView(
                    $request->fullUrl(),
                    $request->userAgent(),
                    $request->ip(),
                    $request->header('referer')
                );
            }
        }

        return $response;
    }
}
