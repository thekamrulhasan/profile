@extends('layouts.admin')

@section('title', 'Manage Experiences')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Experiences</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Experiences</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">Experience Management</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.experiences.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Experience
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search experiences..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('admin.experiences.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    <form id="bulk-form" method="POST" action="{{ route('admin.experiences.bulk') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <select name="action" class="form-control" style="width: auto; display: inline-block;">
                                    <option value="">Bulk Actions</option>
                                    <option value="activate">Activate</option>
                                    <option value="deactivate">Deactivate</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <button type="submit" class="btn btn-secondary" onclick="return confirm('Are you sure?')">
                                    Apply
                                </button>
                            </div>
                        </div>

                        <!-- Experiences Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Company</th>
                                        <th>Position</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($experiences as $experience)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected[]" value="{{ $experience->id }}" class="select-item">
                                        </td>
                                        <td>
                                            <strong>{{ $experience->company }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $experience->location }}</small>
                                        </td>
                                        <td>{{ $experience->position }}</td>
                                        <td>
                                            {{ $experience->start_date->format('M Y') }} - 
                                            {{ $experience->end_date ? $experience->end_date->format('M Y') : 'Present' }}
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $experience->is_active ? 'success' : 'secondary' }}">
                                                {{ $experience->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.experiences.show', $experience) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.experiences.edit', $experience) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.experiences.destroy', $experience) }}" 
                                                      style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No experiences found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $experiences->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.select-item');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
@endpush
@endsection
