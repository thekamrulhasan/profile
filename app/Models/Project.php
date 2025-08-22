<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'featured_image',
        'gallery_images',
        'technologies',
        'project_url',
        'github_url',
        'demo_url',
        'status',
        'start_date',
        'end_date',
        'is_featured',
        'is_published',
        'sort_order',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'gallery_images' => 'array',
            'technologies' => 'array',
            'sort_order' => 'integer',
            'view_count' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'maintenance' => 'secondary',
            default => 'primary',
        };
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->start_date) {
            return null;
        }

        $end = $this->end_date ?? now();
        $diff = $this->start_date->diff($end);

        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        }

        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
        }

        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
    }
}
