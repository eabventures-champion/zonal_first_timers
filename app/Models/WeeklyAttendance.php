<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WeeklyAttendance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Calculate the Sunday-based week number for a given date.
     * Week 1 is the window leading up to (and including) the 1st Sunday of the month.
     * Week 2 is the window after the 1st Sunday up to the 2nd Sunday, etc.
     */
    public static function getWeekNumberForDate($date): int
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        $month = $date->month;
        $year = $date->year;

        $sundays = [];
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            if ($d->isSunday()) {
                $sundays[] = $d->toDateString();
            }
        }

        $dateString = $date->toDateString();
        $weekNumber = count($sundays); // Default to last Sunday window

        foreach ($sundays as $index => $sundayDate) {
            if ($dateString <= $sundayDate) {
                $weekNumber = $index + 1;
                break;
            }
        }

        return $weekNumber;
    }

    protected $fillable = [
        'first_timer_id',
        'member_id',
        'church_id',
        'week_number',
        'month',
        'year',
        'service_date',
        'attended',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'attended' => 'boolean',
            'service_date' => 'date',
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

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
