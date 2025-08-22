@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Users</span>
                    <span class="info-box-number">{{ number_format($stats['total_users']) }}</span>
                    <small class="text-muted">{{ $stats['active_users'] }} active</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-project-diagram"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Projects</span>
                    <span class="info-box-number">{{ number_format($stats['total_projects']) }}</span>
                    <small class="text-muted">{{ $stats['published_projects'] }} published</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-cogs"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Skills</span>
                    <span class="info-box-number">{{ number_format($stats['total_skills']) }}</span>
                    <small class="text-muted">{{ $stats['featured_skills'] }} featured</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-blog"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Blog Posts</span>
                    <span class="info-box-number">{{ number_format($stats['total_blog_posts']) }}</span>
                    <small class="text-muted">{{ $stats['published_blog_posts'] }} published</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Registrations Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Registrations (Last 30 Days)</h3>
                </div>
                <div class="card-body">
                    <canvas id="userRegistrationsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Project Status Distribution -->
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

    <div class="row">
        <!-- Skills by Category -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Skills by Category</h3>
                </div>
                <div class="card-body">
                    <canvas id="skillsCategoryChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activities</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                    <tr>
                                        <td>{{ $activity->user->name ?? 'System' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $activity->action === 'create' ? 'success' : ($activity->action === 'delete' ? 'danger' : 'info') }}">
                                                {{ ucfirst($activity->action) }}
                                            </span>
                                            @if($activity->model_type)
                                                {{ class_basename($activity->model_type) }}
                                            @endif
                                        </td>
                                        <td>{{ $activity->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No recent activities</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Popular Projects -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Most Viewed Projects</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularProjects as $project)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $project) }}">
                                                {{ $project->title }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($project->view_count) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No projects found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Blog Posts -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Most Viewed Blog Posts</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Post</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularPosts as $post)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.blog.show', $post) }}">
                                                {{ $post->title }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($post->view_count) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No blog posts found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Registrations Chart
    const userRegCtx = document.getElementById('userRegistrationsChart').getContext('2d');
    new Chart(userRegCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userRegistrations->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('M d'); })) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($userRegistrations->pluck('count')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
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
    new Chart(projectStatusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($projectStatuses->pluck('status')->map('ucfirst')) !!},
            datasets: [{
                data: {!! json_encode($projectStatuses->pluck('count')) !!},
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Skills Category Chart
    const skillsCategoryCtx = document.getElementById('skillsCategoryChart').getContext('2d');
    new Chart(skillsCategoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($skillsByCategory->pluck('category')->map('ucfirst')) !!},
            datasets: [{
                label: 'Skills Count',
                data: {!! json_encode($skillsByCategory->pluck('count')) !!},
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
});
</script>
@endpush
