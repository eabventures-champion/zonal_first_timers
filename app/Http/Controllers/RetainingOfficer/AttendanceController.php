<?php

namespace App\Http\Controllers\RetainingOfficer;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Models\WeeklyAttendance;
use App\Services\FirstTimerService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private FirstTimerService $firstTimerService
    ) {
    }

    public function index(Request $request)
    {
        $churchId = auth()->user()->church_id;
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        // Calculate Sundays in this month
        $sundays = [];
        $date = \Carbon\Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = \Carbon\Carbon::createFromDate($year, $month, $d);
            if ($currentDate->isSunday()) {
                $sundays[] = [
                    'week_number' => count($sundays) + 1,
                    'date' => $currentDate->toDateString(),
                    'label' => 'W' . (count($sundays) + 1)
                ];
            }
        }

        // Fetch first timers
        $firstTimers = FirstTimer::where('church_id', $churchId)
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

        return view('retaining-officer.attendance.index', compact('groupedAttendance', 'month', 'year', 'sundays'));
    }

    public function toggle(Request $request)
    {
        $churchId = auth()->user()->church_id;
        $val = $request->validate([
            'first_timer_id' => 'required|exists:first_timers,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'week_number' => 'required|integer',
            'service_date' => 'required|date',
            'attended' => 'required|boolean',
        ]);

        $attendance = WeeklyAttendance::updateOrCreate(
            [
                'first_timer_id' => $val['first_timer_id'],
                'church_id' => $churchId,
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

        $ft = FirstTimer::find($val['first_timer_id']);
        $this->firstTimerService->syncMembershipStatus($ft);

        return response()->json([
            'success' => true,
            'total_attended' => $ft->weeklyAttendances()->where('attended', true)->count(),
            'status' => $ft->status
        ]);
    }

    public function create()
    {
        $churchId = auth()->user()->church_id;

        $firstTimers = FirstTimer::where('church_id', $churchId)
            ->whereHas('weeklyAttendances', function ($q) {
                $q->where('attended', true);
            }, '<', 6)
            ->orderBy('full_name')
            ->get();

        return view('retaining-officer.attendance.create', compact('firstTimers'));
    }

    public function store(Request $request)
    {
        $churchId = auth()->user()->church_id;

        $request->validate([
            'week_number' => 'required|integer|min:1|max:5',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'attended' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ]);

        $month = $request->month;
        $year = $request->year;
        $serviceDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');

        // Get all first timers for this church to record both present and absent
        $firstTimers = FirstTimer::where('church_id', $churchId)
            ->whereHas('weeklyAttendances', function ($q) {
                $q->where('attended', true);
            }, '<', 6)
            ->get();

        $attendedIds = array_keys($request->input('attended', []));

        foreach ($firstTimers as $ft) {
            WeeklyAttendance::updateOrCreate(
                [
                    'first_timer_id' => $ft->id,
                    'church_id' => $churchId,
                    'month' => $month,
                    'year' => $year,
                    'week_number' => $request->week_number,
                ],
                [
                    'service_date' => $serviceDate,
                    'attended' => in_array($ft->id, $attendedIds),
                    'notes' => $request->notes,
                    'recorded_by' => auth()->id(),
                ]
            );

            // Trigger membership sync/request detection
            $this->firstTimerService->syncMembershipStatus($ft);
        }

        return redirect()->route('ro.attendance.index')
            ->with('success', 'Attendance recorded successfully.');
    }
}
