<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'proficiency_level',
        'icon',
        'description',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'proficiency_level' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getProficiencyPercentageAttribute(): string
    {
        return $this->proficiency_level . '%';
    }

    public function getProficiencyLevelTextAttribute(): string
    {
        return match (true) {
            $this->proficiency_level >= 90 => 'Expert',
            $this->proficiency_level >= 75 => 'Advanced',
            $this->proficiency_level >= 50 => 'Intermediate',
            $this->proficiency_level >= 25 => 'Beginner',
            default => 'Learning',
        };
    }
}
