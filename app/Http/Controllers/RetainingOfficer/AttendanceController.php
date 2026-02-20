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
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $firstTimers = FirstTimer::where('church_id', $churchId)
            ->with([
                'weeklyAttendances' => function ($q) use ($month, $year) {
                    $q->where('month', $month)->where('year', $year);
                }
            ])
            ->orderBy('full_name')
            ->get();

        $attendanceData = $firstTimers->map(function ($ft) {
            $weeks = [];
            foreach ($ft->weeklyAttendances as $wa) {
                $weeks[$wa->week_number] = $wa->attended;
            }
            return [
                'name' => $ft->full_name,
                'weeks' => $weeks,
            ];
        })->toArray();

        return view('retaining-officer.attendance.index', compact('attendanceData', 'month', 'year'));
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
            'attendance_date' => 'required|date',
            'attended' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ]);

        $date = \Carbon\Carbon::parse($request->attendance_date);
        $month = $date->month;
        $year = $date->year;

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
                    'service_date' => $request->attendance_date,
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
