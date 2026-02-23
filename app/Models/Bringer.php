<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Church;
use App\Models\User;
use App\Models\FirstTimer;
use App\Models\Member;

class Bringer extends Model
{
    protected $table = 'bringers';

    protected $fillable = [
        'church_id',
        'name',
        'contact',
        'user_id',
        'is_ro',
    ];

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function firstTimers(): HasMany
    {
        return $this->hasMany(FirstTimer::class, 'bringer_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'bringer_id');
    }
}
