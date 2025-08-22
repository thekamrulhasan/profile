<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PageView extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'user_agent',
        'ip_address',
        'referrer',
        'user_id',
        'session_id',
        'visited_at'
    ];

    protected $casts = [
        'visited_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes for analytics queries
    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('visited_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visited_at', Carbon::now()->month)
                    ->whereYear('visited_at', Carbon::now()->year);
    }

    public function scopeUniqueVisitors($query)
    {
        return $query->distinct('ip_address');
    }
}
