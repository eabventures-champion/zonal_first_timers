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

        $monthStart = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // 1. Fetch current newcomers (in first_timers table)
        // We show ALL newcomers for this church so they can backfill/record anytime
        $newcomers = FirstTimer::where('church_id', $churchId)
            ->with([
                'weeklyAttendances' => function ($q) use ($month, $year) {
                    $q->where('month', $month)->where('year', $year);
                }
            ])
            ->withCount(['weeklyAttendances as total_attended' => fn($q) => $q->where('attended', true)])
            ->get();

        // 2. Fetch members who were newcomers in the selected month
        $membersAsNewcomers = \App\Models\Member::where('church_id', $churchId)
            ->where(function ($query) use ($monthStart, $monthEnd, $month, $year) {
                $query->where('date_of_visit', '<=', $monthEnd)
                    ->where(function ($q) use ($monthStart) {
                        $q->whereNull('membership_approved_at')
                            ->orWhere('membership_approved_at', '>=', $monthStart);
                    })
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
            ->get();

        // 3. Merge and format
        $allEligible = $newcomers->concat($membersAsNewcomers);

        $groupedAttendance = $allEligible->sortByDesc('date_of_visit')->groupBy(function ($person) {
            return \Carbon\Carbon::parse($person->date_of_visit)->format('F Y');
        })->map(function ($group) {
            return $group->map(function ($person) {
                $weeks = [];
                foreach ($person->weeklyAttendances as $wa) {
                    $weeks[$wa->week_number] = $wa->attended;
                }
                return [
                    'id' => $person->id,
                    'name' => $person->full_name,
                    'is_member' => ($person instanceof \App\Models\Member),
                    'is_readonly' => ($person instanceof \App\Models\Member || $person->total_attended >= 6),
                    'total_attended' => $person->total_attended,
                    'weeks' => $weeks,
                    'join_date' => \Carbon\Carbon::parse($person->date_of_visit)->format('M d, Y'),
                ];
            });
        });

        return view('retaining-officer.attendance.index', compact('groupedAttendance', 'month', 'year', 'sundays'));
    }

    public function toggle(Request $request)
    {
        $churchId = auth()->user()->church_id;
        $val = $request->validate([
            'id' => 'required',
            'is_member' => 'required|boolean',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'week_number' => 'required|integer',
            'service_date' => 'required|date',
            'attended' => 'required|boolean',
        ]);

        if ($val['is_member']) {
            $member = \App\Models\Member::where('church_id', $churchId)->findOrFail($val['id']);
            $firstTimerId = null;
            $memberId = $member->id;

            if ($member->weeklyAttendances()->where('attended', true)->count() >= 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance recording is locked for this person.'
                ], 403);
            }

            $personForResponse = $member;
        } else {
            $firstTimer = FirstTimer::where('church_id', $churchId)->findOrFail($val['id']);
            $firstTimerId = $firstTimer->id;
            $memberId = null;

            if ($firstTimer->weeklyAttendances()->where('attended', true)->count() >= 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance recording is locked for this person.'
                ], 403);
            }

            $personForResponse = $firstTimer;
        }

        $attendance = WeeklyAttendance::updateOrCreate(
            [
                'month' => $val['month'],
                'year' => $val['year'],
                'week_number' => $val['week_number'],
                'first_timer_id' => $firstTimerId,
                'member_id' => $memberId,
            ],
            [
                'church_id' => $churchId,
                'service_date' => $val['service_date'],
                'attended' => $val['attended'],
                'recorded_by' => auth()->id(),
            ]
        );

        if (!$val['is_member']) {
            $ftModel = FirstTimer::findOrFail($firstTimerId);
            $this->firstTimerService->syncMembershipStatus($ftModel);
        }

        return response()->json([
            'success' => true,
            'total_attended' => $personForResponse->weeklyAttendances()->where('attended', true)->count(),
            'status' => $val['is_member'] ? 'Retained' : $personForResponse->status
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
