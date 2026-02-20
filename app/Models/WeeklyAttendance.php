<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_timer_id',
        'member_id',
        'church_id',
        'week_number',
        'month',
        'year',
        'service_date',
        'attended',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'attended' => 'boolean',
            'service_date' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function firstTimer(): BelongsTo
    {
        return $this->belongsTo(FirstTimer::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
