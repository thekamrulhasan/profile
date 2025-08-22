@extends('layouts.admin')

@section('title', 'Skills Management')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Skills</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Skills Management</h3>
            <div class="card-tools">
                @can('content.create')
                    <a href="{{ route('admin.skills.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Skill
                    </a>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search skills..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="{{ route('admin.skills.index') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>

            <!-- Bulk Actions -->
            <form id="bulk-form" method="POST" action="{{ route('admin.skills.bulk-action') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select name="action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="activate">Activate</option>
                            <option value="deactivate">Deactivate</option>
                            <option value="feature">Feature</option>
                            <option value="unfeature">Unfeature</option>
                            <option value="delete">Delete</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure?')">
                            Apply to Selected
                        </button>
                    </div>
                </div>

                <!-- Skills Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Proficiency</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Sort Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($skills as $skill)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="skills[]" value="{{ $skill->id }}" class="skill-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($skill->icon)
                                                <i class="{{ $skill->icon }} mr-2"></i>
                                            @endif
                                            <strong>{{ $skill->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($skill->category) }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $skill->proficiency_level }}%" 
                                                 aria-valuenow="{{ $skill->proficiency_level }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ $skill->proficiency_level }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($skill->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($skill->is_featured)
                                            <span class="badge badge-warning">Featured</span>
                                        @else
                                            <span class="badge badge-secondary">Regular</span>
                                        @endif
                                    </td>
                                    <td>{{ $skill->sort_order }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.skills.show', $skill) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @can('content.edit')
                                                <a href="{{ route('admin.skills.edit', $skill) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            
                                            @can('content.delete')
                                                <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this skill?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No skills found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $skills->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const skillCheckboxes = document.querySelectorAll('.skill-checkbox');

    selectAllCheckbox.addEventListener('change', function() {
        skillCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all checkbox when individual checkboxes change
    skillCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.skill-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === skillCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < skillCheckboxes.length;
        });
    });
});
</script>
@endpush
