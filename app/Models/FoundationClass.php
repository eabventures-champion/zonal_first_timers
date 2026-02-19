<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoundationClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_number',
        'description',
    ];

    // ── Relationships ──────────────────────────────────────

    public function attendances(): HasMany
    {
        return $this->hasMany(FoundationAttendance::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeOrdered($query)
    {
        return $query->orderBy('class_number');
    }
}
