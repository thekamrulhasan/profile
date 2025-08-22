<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($request->user()?->id === $this->id || $request->user()?->isAdmin(), $this->email),
            'role' => new RoleResource($this->whenLoaded('role')),
            'is_active' => $this->when($request->user()?->isAdmin(), $this->is_active),
            'last_login_at' => $this->when($request->user()?->isAdmin(), $this->last_login_at?->toISOString()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
