<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::deleting(function ($member) {
            // Clean up attendances using each() to trigger their own events if any
            $member->weeklyAttendances()->each(fn($a) => $a->delete());
            $member->foundationAttendances()->each(fn($a) => $a->delete());
        });

        static::restoring(function ($member) {
            // Restore attendances that were deleted when the soul was deleted
            $member->weeklyAttendances()->onlyTrashed()->each(fn($a) => $a->restore());
            $member->foundationAttendances()->onlyTrashed()->each(fn($a) => $a->restore());
        });
    }

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
        'born_again',
        'water_baptism',
        'prayer_requests',
        'date_of_visit',
        'church_event',
        'status',
        'membership_requested_at',
        'membership_approved_at',
        'acknowledged_at',
        'retaining_officer_id',
        'user_id',
        'migrated_at',
        'bringer_id',
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
            'acknowledged_at' => 'datetime',
            'born_again' => 'boolean',
            'water_baptism' => 'boolean',
            'migrated_at' => 'datetime',
            'bringer_id' => 'integer',
        ];
    }

    public function bringer(): BelongsTo
    {
        return $this->belongsTo(Bringer::class);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function getAttendanceDatesAttribute(): array
    {
        return $this->weeklyAttendances()
            ->where('attended', true)
            ->orderByDesc('service_date')
            ->pluck('service_date')
            ->map(fn($date) => $date->format('M d, Y'))
            ->toArray();
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

    public function getCurrentFoundationLevelAttribute(): string
    {
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
}
