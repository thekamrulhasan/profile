<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Experience extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'position',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'location',
        'company_logo',
        'technologies',
        'achievements',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'is_active' => 'boolean',
            'technologies' => 'array',
            'achievements' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('start_date', 'desc')->orderBy('sort_order');
    }

    public function getDurationAttribute(): string
    {
        $start = $this->start_date;
        $end = $this->is_current ? now() : $this->end_date;

        if (!$end) {
            return 'Present';
        }

        $diff = $start->diff($end);
        $years = $diff->y;
        $months = $diff->m;

        $duration = [];
        if ($years > 0) {
            $duration[] = $years . ' year' . ($years > 1 ? 's' : '');
        }
        if ($months > 0) {
            $duration[] = $months . ' month' . ($months > 1 ? 's' : '');
        }

        return empty($duration) ? '1 month' : implode(' ', $duration);
    }

    public function getFormattedDateRangeAttribute(): string
    {
        $start = $this->start_date->format('M Y');
        $end = $this->is_current ? 'Present' : $this->end_date?->format('M Y');

        return $start . ' - ' . $end;
    }
}
