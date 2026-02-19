<?php

namespace App\Http\Controllers\RetainingOfficer;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Models\WeeklyAttendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $churchId = auth()->user()->church_id;

        $firstTimers = FirstTimer::where('church_id', $churchId)
            ->with('weeklyAttendances')
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

        return view('retaining-officer.attendance.index', compact('attendanceData'));
    }

    public function create()
    {
        $churchId = auth()->user()->church_id;

        $firstTimers = FirstTimer::where('church_id', $churchId)
            ->whereIn('status', ['New', 'In Progress'])
            ->orderBy('full_name')
            ->get();

        return view('retaining-officer.attendance.create', compact('firstTimers'));
    }

    public function store(Request $request)
    {
        $churchId = auth()->user()->church_id;

        $request->validate([
            'week_number' => 'required|integer|min:1|max:12',
            'attendance_date' => 'required|date',
            'attended' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get all first timers for this church to record both present and absent
        $firstTimers = FirstTimer::where('church_id', $churchId)
            ->whereIn('status', ['New', 'In Progress'])
            ->get();

        $attendedIds = array_keys($request->input('attended', []));

        foreach ($firstTimers as $ft) {
            WeeklyAttendance::updateOrCreate(
                [
                    'first_timer_id' => $ft->id,
                    'church_id' => $churchId,
                    'week_number' => $request->week_number,
                ],
                [
                    'service_date' => $request->attendance_date,
                    'attended' => in_array($ft->id, $attendedIds),
                    'notes' => $request->notes,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('ro.attendance.index')
            ->with('success', 'Attendance recorded successfully.');
    }
}
