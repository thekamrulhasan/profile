@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Analytics Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button class="btn btn-primary" onclick="exportAnalytics('json')">
                            <i class="fas fa-download"></i> Export JSON
                        </button>
                        <button class="btn btn-success" onclick="exportAnalytics('csv')">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Overview Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $analytics['overview']['total_users'] }}</h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $analytics['overview']['total_projects'] }}</h3>
                            <p>Total Projects</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $analytics['overview']['total_blog_posts'] }}</h3>
                            <p>Blog Posts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-blog"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $analytics['overview']['total_skills'] }}</h3>
                            <p>Skills</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User Growth</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="userGrowthChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Project Status Distribution</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="projectStatusChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills and Performance Row -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Skill Distribution by Category</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="skillDistributionChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Performance Metrics</h3>
                        </div>
                        <div class="card-body">
                            <div class="progress-group">
                                <span class="progress-text">Uptime</span>
                                <span class="float-right"><b>{{ $analytics['performance_metrics']['uptime_percentage'] }}%</b></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" style="width: {{ $analytics['performance_metrics']['uptime_percentage'] }}%"></div>
                                </div>
                            </div>
                            <div class="progress-group">
                                <span class="progress-text">Cache Hit Rate</span>
                                <span class="float-right"><b>{{ number_format($analytics['performance_metrics']['cache_hit_rate'] * 100, 1) }}%</b></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-info" style="width: {{ $analytics['performance_metrics']['cache_hit_rate'] * 100 }}%"></div>
                                </div>
                            </div>
                            <div class="progress-group">
                                <span class="progress-text">Error Rate</span>
                                <span class="float-right"><b>{{ number_format($analytics['performance_metrics']['error_rate'] * 100, 2) }}%</b></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-danger" style="width: {{ $analytics['performance_metrics']['error_rate'] * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activity</h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($analytics['activity_timeline'] as $activity)
                                <div class="time-label">
                                    <span class="bg-blue">{{ $activity['created_at'] }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-user bg-green"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $activity['created_at'] }}</span>
                                        <h3 class="timeline-header">
                                            <strong>{{ $activity['user'] }}</strong> {{ $activity['action'] }}
                                        </h3>
                                        <div class="timeline-body">
                                            {{ class_basename($activity['model']) }} #{{ $activity['model_id'] }}
                                            <small class="text-muted">from {{ $activity['ip_address'] }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
const userGrowthChart = new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($analytics['user_growth']->pluck('month')) !!},
        datasets: [{
            label: 'New Users',
            data: {!! json_encode($analytics['user_growth']->pluck('users')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Cumulative Users',
            data: {!! json_encode($analytics['user_growth']->pluck('cumulative')) !!},
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Project Status Chart
const projectStatusCtx = document.getElementById('projectStatusChart').getContext('2d');
const projectStatusChart = new Chart(projectStatusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($analytics['project_stats']['by_status']->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($analytics['project_stats']['by_status']->pluck('count')) !!},
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 205, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Skill Distribution Chart
const skillDistributionCtx = document.getElementById('skillDistributionChart').getContext('2d');
const skillCategories = {!! json_encode(array_keys($analytics['skill_distribution']->toArray())) !!};
const skillData = {!! json_encode(array_values($analytics['skill_distribution']->map(function($skills) { return $skills->sum('count'); })->toArray())) !!};

const skillDistributionChart = new Chart(skillDistributionCtx, {
    type: 'bar',
    data: {
        labels: skillCategories,
        datasets: [{
            label: 'Skills Count',
            data: skillData,
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Export Functions
function exportAnalytics(format) {
    window.location.href = `/admin/analytics/export?format=${format}`;
}

// Auto-refresh analytics every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endpush
@endsection
