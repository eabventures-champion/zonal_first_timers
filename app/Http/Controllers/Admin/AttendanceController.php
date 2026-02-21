<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Church;
use App\Models\ChurchGroup;
use App\Models\FirstTimer;
use App\Models\WeeklyAttendance;
use App\Services\FirstTimerService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct(
        private FirstTimerService $firstTimerService
    ) {
    }

    public function index()
    {
        $groups = ChurchGroup::with([
            'churches' => function ($query) {
                $query->withCount([
                    'firstTimers',
                    'members as members_count'
                ]);
            }
        ])->get();

        return view('admin.attendance.index', compact('groups'));
    }

    public function show(Church $church, Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        // Calculate Sundays in this month
        $sundays = [];
        $date = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = Carbon::createFromDate($year, $month, $d);
            if ($currentDate->isSunday()) {
                $sundays[] = [
                    'week_number' => count($sundays) + 1,
                    'date' => $currentDate->toDateString(),
                    'label' => 'W' . (count($sundays) + 1)
                ];
            }
        }

        // Fetch first timers for specific church
        $firstTimers = FirstTimer::where('church_id', $church->id)
            ->where(function ($query) use ($month, $year) {
                $query->whereMonth('date_of_visit', $month)
                    ->whereYear('date_of_visit', $year)
                    ->orWhereHas('weeklyAttendances', function ($q) use ($month, $year) {
                        $q->where('month', $month)->where('year', $year);
                    });
            })
            ->with([
                'weeklyAttendances' => function ($q) use ($month, $year) {
                    $q->where('month', $month)->where('year', $year);
                }
            ])
            ->withCount(['weeklyAttendances as total_attended' => fn($q) => $q->where('attended', true)])
            ->orderByDesc('date_of_visit')
            ->get();

        $groupedAttendance = $firstTimers->groupBy(function ($ft) {
            return $ft->date_of_visit->format('F Y');
        })->map(function ($group) {
            return $group->map(function ($ft) {
                $weeks = [];
                foreach ($ft->weeklyAttendances as $wa) {
                    $weeks[$wa->week_number] = $wa->attended;
                }
                return [
                    'id' => $ft->id,
                    'name' => $ft->full_name,
                    'total_attended' => $ft->total_attended,
                    'weeks' => $weeks,
                    'join_date' => $ft->date_of_visit->format('M d, Y'),
                ];
            });
        });

        return view('admin.attendance.show', compact('church', 'groupedAttendance', 'month', 'year', 'sundays'));
    }

    public function toggle(Request $request)
    {
        $val = $request->validate([
            'first_timer_id' => 'required|exists:first_timers,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'week_number' => 'required|integer',
            'service_date' => 'required|date',
            'attended' => 'required|boolean',
        ]);

        $firstTimer = FirstTimer::findOrFail($val['first_timer_id']);

        $attendance = WeeklyAttendance::updateOrCreate(
            [
                'first_timer_id' => $val['first_timer_id'],
                'church_id' => $firstTimer->church_id,
                'month' => $val['month'],
                'year' => $val['year'],
                'week_number' => $val['week_number'],
            ],
            [
                'service_date' => $val['service_date'],
                'attended' => $val['attended'],
                'recorded_by' => auth()->id(),
            ]
        );

        $this->firstTimerService->syncMembershipStatus($firstTimer);

        return response()->json([
            'success' => true,
            'total_attended' => $firstTimer->weeklyAttendances()->where('attended', true)->count(),
            'status' => $firstTimer->status
        ]);
    }
}
