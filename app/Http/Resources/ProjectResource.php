<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->when($request->routeIs('api.projects.show'), $this->description),
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'gallery_images' => $this->gallery_images ? 
                collect($this->gallery_images)->map(fn($image) => asset('storage/' . $image))->toArray() : [],
            'technologies' => $this->technologies ?? [],
            'project_url' => $this->project_url,
            'github_url' => $this->github_url,
            'demo_url' => $this->demo_url,
            'status' => $this->status,
            'status_badge_color' => $this->status_badge_color,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'duration' => $this->duration,
            'is_featured' => $this->is_featured,
            'view_count' => $this->view_count,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
