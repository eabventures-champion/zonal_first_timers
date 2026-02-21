<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ChurchCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'zonal_pastor_name',
        'zonal_pastor_contact',
        'created_by',
        'updated_by',
    ];

    // ── Relationships ──────────────────────────────────────

    public function groups(): HasMany
    {
        return $this->hasMany(ChurchGroup::class);
    }

    public function churches(): HasManyThrough
    {
        return $this->hasManyThrough(Church::class, ChurchGroup::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Accessors ──────────────────────────────────────────

    public function getChurchCountAttribute(): int
    {
        return $this->groups->sum(fn($group) => $group->churches->count());
    }
}
