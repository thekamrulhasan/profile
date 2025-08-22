@extends('layouts.admin')

@section('title', 'Manage Blog Posts')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Blog Posts</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Blog Posts</li>
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
                            <h3 class="card-title">Blog Management</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> New Post
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search posts..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Blog Posts Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Published</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($posts as $post)
                                <tr>
                                    <td>
                                        <strong>{{ $post->title }}</strong>
                                        @if($post->featured_image)
                                        <br><small class="text-muted"><i class="fas fa-image"></i> Has featured image</small>
                                        @endif
                                    </td>
                                    <td>{{ $post->author->name }}</td>
                                    <td>
                                        @if($post->category)
                                        <span class="badge badge-info">{{ $post->category }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $post->status === 'published' ? 'success' : 'warning' }}">
                                            {{ ucfirst($post->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($post->published_at)
                                        {{ $post->published_at->format('M d, Y') }}
                                        @else
                                        <span class="text-muted">Not published</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.blog.show', $post) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.blog.edit', $post) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" 
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
                                    <td colspan="6" class="text-center">No blog posts found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $posts->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
