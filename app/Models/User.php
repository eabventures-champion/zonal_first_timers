<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Bringer;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected static function booted()
    {
        static::deleting(function ($user) {
            // Handle Bringer records
            $bringers = \App\Models\Bringer::where('user_id', $user->id)->get();
            foreach ($bringers as $bringer) {
                /** @var \App\Models\Bringer $bringer */
                // Nullify bringer_id on souls so we don't block deletion
                \App\Models\FirstTimer::withTrashed()->where('bringer_id', $bringer->id)->update(['bringer_id' => null]);
                \App\Models\Member::withTrashed()->where('bringer_id', $bringer->id)->update(['bringer_id' => null]);

                // If it's a manual bringer or RO fallback, we can delete it entirely
                $bringer->delete();
            }

            // Nullify RO assignments
            \App\Models\FirstTimer::withTrashed()->where('retaining_officer_id', $user->id)->update(['retaining_officer_id' => null]);
            \App\Models\Member::withTrashed()->where('retaining_officer_id', $user->id)->update(['retaining_officer_id' => null]);
            \App\Models\Church::withTrashed()->where('retaining_officer_id', $user->id)->update(['retaining_officer_id' => null]);

            // Nullify other user associations
            \App\Models\FirstTimer::withTrashed()->where('user_id', $user->id)->update(['user_id' => null]);
            \App\Models\Member::withTrashed()->where('user_id', $user->id)->update(['user_id' => null]);
            \App\Models\WeeklyAttendance::where('recorded_by', $user->id)->update(['recorded_by' => null]);

            // Nullify audit fields (created_by, updated_by)
            \App\Models\FirstTimer::withTrashed()->where('created_by', $user->id)->update(['created_by' => null]);
            \App\Models\FirstTimer::withTrashed()->where('updated_by', $user->id)->update(['updated_by' => null]);
            \App\Models\Member::withTrashed()->where('created_by', $user->id)->update(['created_by' => null]);
            \App\Models\Member::withTrashed()->where('updated_by', $user->id)->update(['updated_by' => null]);
            \App\Models\Church::withTrashed()->where('created_by', $user->id)->update(['created_by' => null]);
            \App\Models\Church::withTrashed()->where('updated_by', $user->id)->update(['updated_by' => null]);
            \App\Models\ChurchGroup::withTrashed()->where('created_by', $user->id)->update(['created_by' => null]);
            \App\Models\ChurchGroup::withTrashed()->where('updated_by', $user->id)->update(['updated_by' => null]);
            \App\Models\ChurchCategory::withTrashed()->where('created_by', $user->id)->update(['created_by' => null]);
            \App\Models\ChurchCategory::withTrashed()->where('updated_by', $user->id)->update(['updated_by' => null]);
        });
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'church_id',
        'phone',
        'deletion_requested_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deletion_requested_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function assignedFirstTimers(): HasMany
    {
        return $this->hasMany(FirstTimer::class, 'retaining_officer_id');
    }

    public function managedChurches(): HasMany
    {
        return $this->hasMany(Church::class, 'retaining_officer_id');
    }

    public function bringer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Bringer::class);
    }

    /**
     * Check if this user is also a bringer (via direct user_id link or via RO church assignment).
     */
    public function isBringer(): bool
    {
        // Direct link via user_id
        if ($this->bringer) {
            return true;
        }

        // RO bringers may not have user_id linked — check by church + is_ro
        if ($this->hasRole('Retaining Officer') && $this->church_id) {
            return Bringer::where('church_id', $this->church_id)
                ->where('is_ro', true)
                ->exists();
        }

        return false;
    }

    // ── Helpers ────────────────────────────────────────────

    public function isDeletionPending(): bool
    {
        return !is_null($this->deletion_requested_at);
    }

    public static function getPendingDeletionCount(): int
    {
        return self::whereNotNull('deletion_requested_at')->count();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isRetainingOfficer(): bool
    {
        return $this->hasRole('Retaining Officer');
    }

    public function isOtherChurchRO(): bool
    {
        // Admins and Super Admins should have access to registration forms
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        if (!$this->isRetainingOfficer() || !$this->church_id) {
            return false;
        }

        return trim(strtoupper($this->church->group->category->name)) === 'OTHER CHURCHES';
    }
}
