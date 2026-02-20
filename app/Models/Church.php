<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Church extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'church_group_id',
        'name',
        'address',
        'retaining_officer_id',
        'created_by',
        'updated_by',
    ];

    // ── Relationships ──────────────────────────────────────

    public function group(): BelongsTo
    {
        return $this->belongsTo(ChurchGroup::class, 'church_group_id');
    }

    public function retainingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retaining_officer_id');
    }

    public function firstTimers(): HasMany
    {
        return $this->hasMany(FirstTimer::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'church_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
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

    public function scopeWithStats($query)
    {
        return $query->withCount([
            'firstTimers',
            'firstTimers as new_first_timers_count' => fn($q) => $q->where('status', 'New'),
            'firstTimers as developing_count' => fn($q) => $q->where('status', 'Developing'),
            'members as members_count'
        ]);
    }
}
