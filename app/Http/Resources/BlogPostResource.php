<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->when($request->routeIs('api.blog.show'), $this->content),
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'tags' => $this->tags ?? [],
            'status' => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'view_count' => $this->view_count,
            'read_time' => $this->read_time,
            'author' => new UserResource($this->whenLoaded('user')),
            'meta_data' => $this->when($request->routeIs('api.blog.show'), $this->meta_data),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
