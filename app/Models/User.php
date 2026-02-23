<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
        if (!$this->isRetainingOfficer() || !$this->church_id) {
            return false;
        }

        return strtoupper($this->church->group->category->name) === 'OTHER CHURCHES';
    }
}
