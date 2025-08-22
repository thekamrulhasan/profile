<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:media.view')->only(['index', 'show']);
        $this->middleware('permission:media.upload')->only(['create', 'store', 'upload']);
        $this->middleware('permission:media.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = MediaFile::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        // Filter by mime type
        if ($request->filled('type')) {
            $type = $request->type;
            $query->where('mime_type', 'like', "{$type}/%");
        }

        $mediaFiles = $query->orderBy('created_at', 'desc')->paginate(24);

        return view('admin.media.index', compact('mediaFiles'));
    }

    public function create()
    {
        return view('admin.media.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'files.*' => ['required', 'file', 'max:10240'], // 10MB max
            'alt_text.*' => ['nullable', 'string', 'max:255'],
            'description.*' => ['nullable', 'string'],
        ]);

        $uploadedFiles = [];

        foreach ($request->file('files') as $index => $file) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('media', $filename, 'public');

            $mediaFile = MediaFile::create([
                'user_id' => auth()->id(),
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'path' => $path,
                'disk' => 'public',
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'metadata' => $this->getFileMetadata($file),
                'alt_text' => $request->alt_text[$index] ?? null,
                'description' => $request->description[$index] ?? null,
            ]);

            $uploadedFiles[] = $mediaFile;
        }

        return redirect()->route('admin.media.index')
                        ->with('success', count($uploadedFiles) . ' file(s) uploaded successfully.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10MB max
        ]);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $filename, 'public');

        $mediaFile = MediaFile::create([
            'user_id' => auth()->id(),
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'metadata' => $this->getFileMetadata($file),
        ]);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $mediaFile->id,
                'url' => Storage::disk('public')->url($mediaFile->path),
                'filename' => $mediaFile->original_filename,
                'size' => $mediaFile->size,
                'mime_type' => $mediaFile->mime_type,
            ]
        ]);
    }

    public function show(MediaFile $media)
    {
        return view('admin.media.show', compact('media'));
    }

    public function update(Request $request, MediaFile $media)
    {
        $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $media->update([
            'alt_text' => $request->alt_text,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.media.index')
                        ->with('success', 'Media file updated successfully.');
    }

    public function destroy(MediaFile $media)
    {
        // Delete the physical file
        Storage::disk($media->disk)->delete($media->path);

        // Delete the database record
        $media->delete();

        return redirect()->route('admin.media.index')
                        ->with('success', 'Media file deleted successfully.');
    }

    private function getFileMetadata($file)
    {
        $metadata = [];

        if (str_starts_with($file->getMimeType(), 'image/')) {
            $imageSize = getimagesizefromstring($file->get());
            if ($imageSize) {
                $metadata['width'] = $imageSize[0];
                $metadata['height'] = $imageSize[1];
            }
        }

        return $metadata;
    }
}
