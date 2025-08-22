<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'proficiency_level' => $this->proficiency_level,
            'proficiency_percentage' => $this->proficiency_percentage,
            'proficiency_level_text' => $this->proficiency_level_text,
            'icon' => $this->icon,
            'description' => $this->description,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
