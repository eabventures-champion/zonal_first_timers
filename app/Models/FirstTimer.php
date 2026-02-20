<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FirstTimer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'church_id',
        'full_name',
        'primary_contact',
        'alternate_contact',
        'gender',
        'date_of_birth',
        'age',
        'residential_address',
        'occupation',
        'marital_status',
        'email',
        'bringer_name',
        'bringer_contact',
        'bringer_fellowship',
        'born_again',
        'water_baptism',
        'prayer_requests',
        'date_of_visit',
        'church_event',
        'status',
        'membership_requested_at',
        'membership_approved_at',
        'retaining_officer_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_visit' => 'date',
            'membership_requested_at' => 'datetime',
            'membership_approved_at' => 'datetime',
            'born_again' => 'boolean',
            'water_baptism' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function retainingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retaining_officer_id');
    }

    public function foundationAttendances(): HasMany
    {
        return $this->hasMany(FoundationAttendance::class);
    }

    public function weeklyAttendances(): HasMany
    {
        return $this->hasMany(WeeklyAttendance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeForChurch($query, $churchId)
    {
        return $query->where('church_id', $churchId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Accessors ──────────────────────────────────────────

    public function getFoundationProgressAttribute(): float
    {
        $total = FoundationClass::count();
        if ($total === 0)
            return 0;

        $completed = $this->foundationAttendances()->where('completed', true)->count();
        return round(($completed / $total) * 100, 1);
    }

    public function getAttendanceRateAttribute(): float
    {
        $total = $this->weeklyAttendances()->count();
        if ($total === 0)
            return 0;

        $attended = $this->weeklyAttendances()->where('attended', true)->count();
        return round(($attended / $total) * 100, 1);
    }

    public function getStatusAttribute($value): string
    {
        // Use pre-calculated attribute if available (eager loaded via withCount)
        // Check for 'total_attended' (custom alias) or default 'weekly_attendances_count'
        $attendedCount = $this->attributes['total_attended']
            ?? $this->attributes['weekly_attendances_count']
            ?? null;

        if ($attendedCount === null) {
            $attendedCount = $this->weeklyAttendances()->where('attended', true)->count();
        }

        if ($attendedCount >= 6) {
            return 'Retained';
        } elseif ($attendedCount >= 2) {
            return 'Developing';
        }

        return 'New';
    }

    public function getIsReadOnlyAttribute(): bool
    {
        return $this->status === 'Retained';
    }

    public function getAttendanceDatesAttribute(): array
    {
        return $this->weeklyAttendances()
            ->where('attended', true)
            ->orderByDesc('service_date')
            ->pluck('service_date')
            ->map(fn($date) => $date->format('M d, Y'))
            ->toArray();
    }

    public function getCurrentFoundationLevelAttribute(): string
    {
        if ($this->status === 'Retained') {
            return 'Completed';
        }

        $latestAttendance = $this->foundationAttendances()
            ->where('completed', true)
            ->with('foundationClass')
            ->get()
            ->sortByDesc(fn($a) => $a->foundationClass->class_number)
            ->first();

        if (!$latestAttendance) {
            return 'Not Started';
        }

        return $latestAttendance->foundationClass->name;
    }

    public function getFoundationSchoolStatusAttribute(): string
    {
        $totalClasses = \App\Models\FoundationClass::count();
        $completedClasses = $this->foundationAttendances->where('completed', true)->count();

        if ($totalClasses === 0)
            return 'not yet';
        if ($completedClasses >= $totalClasses)
            return 'completed';
        if ($completedClasses > 0)
            return 'in-progress';

        return 'not yet';
    }
}
