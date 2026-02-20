<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoundationAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_timer_id',
        'member_id',
        'foundation_class_id',
        'attended',
        'completed',
        'attendance_date',
    ];

    protected function casts(): array
    {
        return [
            'attended' => 'boolean',
            'completed' => 'boolean',
            'attendance_date' => 'date',
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

    public function foundationClass(): BelongsTo
    {
        return $this->belongsTo(FoundationClass::class);
    }
}
