<?php

use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Admin\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::prefix('v1')->group(function () {
    // Portfolio overview
    Route::get('/portfolio/overview', [PortfolioController::class, 'overview']);
    Route::get('/portfolio/contact', [PortfolioController::class, 'contact']);
    Route::get('/portfolio/seo', [PortfolioController::class, 'seo']);

    // Skills
    Route::get('/skills', [SkillController::class, 'index']);
    Route::get('/skills/categories', [SkillController::class, 'categories']);
    Route::get('/skills/featured', [SkillController::class, 'featured']);
    Route::get('/skills/{skill}', [SkillController::class, 'show']);

    // Experience
    Route::get('/experience', [ExperienceController::class, 'index']);
    Route::get('/experience/current', [ExperienceController::class, 'current']);
    Route::get('/experience/timeline', [ExperienceController::class, 'timeline']);
    Route::get('/experience/{experience}', [ExperienceController::class, 'show']);

    // Projects
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/featured', [ProjectController::class, 'featured']);
    Route::get('/projects/technologies', [ProjectController::class, 'technologies']);
    Route::get('/projects/statuses', [ProjectController::class, 'statuses']);
    Route::get('/projects/{project:slug}', [ProjectController::class, 'show']);

    // Blog
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/blog/recent', [BlogController::class, 'recent']);
    Route::get('/blog/tags', [BlogController::class, 'tags']);
    Route::get('/blog/archive', [BlogController::class, 'archive']);
    Route::get('/blog/{blogPost:slug}', [BlogController::class, 'show']);

    // Analytics
    Route::post('/analytics/track', function (Request $request) {
        app(\App\Services\AnalyticsService::class)->trackPageView(
            $request->input('url'),
            $request->userAgent(),
            $request->ip(),
            $request->input('referrer')
        );
        return response()->json(['status' => 'tracked']);
    });
});

// Authenticated API routes
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Analytics API endpoints for authenticated users
    Route::middleware(['role:admin,super_admin'])->prefix('analytics')->group(function () {
        Route::get('/', [AnalyticsController::class, 'api']);
        Route::get('/overview', [AnalyticsController::class, 'api']);
        Route::get('/export', [AnalyticsController::class, 'export']);
    });
});