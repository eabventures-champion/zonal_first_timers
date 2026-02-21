<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ChurchGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'church_category_id',
        'name',
        'pastor_name',
        'pastor_contact',
        'created_by',
        'updated_by',
    ];

    // ── Relationships ──────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChurchCategory::class, 'church_category_id');
    }

    public function churches(): HasMany
    {
        return $this->hasMany(Church::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
