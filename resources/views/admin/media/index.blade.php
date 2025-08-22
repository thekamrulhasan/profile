@extends('layouts.admin')

@section('title', 'Media Library')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Media Library</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Media Library</h3>
            <div class="card-tools">
                @can('media.upload')
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                        <i class="fas fa-upload"></i> Upload Files
                    </button>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search files..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                            <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Videos</option>
                            <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                            <option value="application" {{ request('type') === 'application' ? 'selected' : '' }}>Documents</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="{{ route('admin.media.index') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>

            <!-- Media Grid -->
            <div class="row">
                @forelse($mediaFiles as $media)
                    <div class="col-md-3 col-sm-4 col-6 mb-4">
                        <div class="card">
                            <div class="card-body p-2">
                                @if(str_starts_with($media->mime_type, 'image/'))
                                    <img src="{{ Storage::disk('public')->url($media->path) }}" 
                                         alt="{{ $media->alt_text }}" 
                                         class="img-fluid rounded mb-2"
                                         style="height: 150px; width: 100%; object-fit: cover;">
                                @else
                                    <div class="text-center py-4" style="height: 150px; background: #f8f9fa; border-radius: 0.25rem;">
                                        <i class="fas fa-file fa-3x text-muted mb-2"></i>
                                        <div class="text-muted small">{{ strtoupper(pathinfo($media->original_filename, PATHINFO_EXTENSION)) }}</div>
                                    </div>
                                @endif

                                <div class="text-truncate">
                                    <strong class="small">{{ $media->original_filename }}</strong>
                                </div>
                                <div class="text-muted small">
                                    {{ number_format($media->size / 1024, 1) }} KB
                                </div>
                                <div class="text-muted small">
                                    {{ $media->created_at->format('M d, Y') }}
                                </div>

                                <div class="mt-2">
                                    <div class="btn-group btn-group-sm w-100" role="group">
                                        <a href="{{ route('admin.media.show', $media) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-secondary" 
                                                onclick="copyToClipboard('{{ Storage::disk('public')->url($media->path) }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        @can('media.delete')
                                            <form method="POST" action="{{ route('admin.media.destroy', $media) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this file?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-images fa-3x mb-3"></i>
                            <h5>No media files found</h5>
                            <p>Upload some files to get started.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $mediaFiles->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Files</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Files</label>
                            <input type="file" class="form-control-file" name="files[]" multiple required>
                            <small class="form-text text-muted">Maximum file size: 10MB per file</small>
                        </div>

                        <div id="file-previews"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Files</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        Toast.fire({
            icon: 'success',
            title: 'URL copied to clipboard!'
        });
    });
}

// File preview functionality
document.querySelector('input[name="files[]"]').addEventListener('change', function(e) {
    const previews = document.getElementById('file-previews');
    previews.innerHTML = '';

    Array.from(e.target.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'mb-3 p-3 border rounded';
        
        let preview = '';
        if (file.type.startsWith('image/')) {
            const url = URL.createObjectURL(file);
            preview = `<img src="${url}" class="img-thumbnail mr-3" style="width: 100px; height: 100px; object-fit: cover;">`;
        } else {
            preview = `<div class="d-inline-block mr-3 text-center" style="width: 100px; height: 100px; background: #f8f9fa; border-radius: 0.25rem; line-height: 100px;">
                <i class="fas fa-file fa-2x text-muted"></i>
            </div>`;
        }

        div.innerHTML = `
            <div class="d-flex align-items-start">
                ${preview}
                <div class="flex-grow-1">
                    <strong>${file.name}</strong><br>
                    <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                    <div class="mt-2">
                        <input type="text" class="form-control form-control-sm mb-1" 
                               name="alt_text[${index}]" placeholder="Alt text (for images)">
                        <textarea class="form-control form-control-sm" 
                                  name="description[${index}]" rows="2" placeholder="Description"></textarea>
                    </div>
                </div>
            </div>
        `;
        
        previews.appendChild(div);
    });
});
</script>
@endpush
