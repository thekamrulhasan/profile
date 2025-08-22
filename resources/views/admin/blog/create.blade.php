@extends('layouts.admin')

@section('title', 'Create Blog Post')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog Posts</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create New Blog Post</h3>
        </div>

        <form method="POST" action="{{ route('admin.blog.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="excerpt">Excerpt</label>
                    <textarea class="form-control @error('excerpt') is-invalid @enderror"
                              id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="content">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content') is-invalid @enderror"
                              id="content" name="content" rows="10">{{ old('content') }}</textarea>
                    @error('content')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="featured_image">Featured Image</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('featured_image') is-invalid @enderror"
                                   id="featured_image" name="featured_image">
                            <label class="custom-file-label" for="featured_image">Choose file</label>
                        </div>
                    </div>
                    @error('featured_image')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror"
                                   id="category" name="category" value="{{ old('category') }}"
                                   list="categories">
                            <datalist id="categories">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">
                                @endforeach
                            </datalist>
                            @error('category')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tags">Tags (comma-separated)</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                   id="tags" name="tags" value="{{ old('tags') }}">
                            @error('tags')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="published_at">Published At</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                                   id="published_at" name="published_at" value="{{ old('published_at') }}">
                            @error('published_at')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_featured"
                               name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_featured">Featured Post</label>
                    </div>
                </div>

                <hr>
                <h5>SEO Meta Data</h5>
                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                           id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                    @error('meta_title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror"
                              id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                    @error('meta_description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Post</button>
                <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        plugins: 'advcode advlist anchor autolink autosave charmap code codesample directionality emoticons fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table template visualblocks visualchars wordcount',
        toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save print | insertfile image media template link anchor codesample | ltr rtl',
        height: 500,
        menubar: true,
        statusbar: true,
        quickbars_insert_toolbar: 'quickimage quicktable',
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
        entity_encoding: 'raw',
        images_upload_url: '/admin/media/upload', // Your media upload endpoint
        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '/admin/media/upload');

                xhr.upload.onprogress = (e) => {
                    progress(e.loaded / e.total * 100);
                };

                xhr.onload = () => {
                    if (xhr.status === 403) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }

                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }

                    const json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.file.url != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }

                    resolve(json.file.url);
                };

                xhr.onerror = () => {
                    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };

                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                xhr.send(formData);
            });
        }
    });

    // Update custom file input label
    document.getElementById('featured_image').addEventListener('change', function() {
        var fileName = this.files[0].name;
        var nextSibling = this.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>
@endpush