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

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // 1. Fetch current newcomers (in first_timers table)
        // We show ALL newcomers for this church so they can backfill/record anytime
        $newcomers = FirstTimer::where('church_id', $church->id)
            ->with([
                'weeklyAttendances' => function ($q) use ($month, $year) {
                    $q->where('month', $month)->where('year', $year);
                }
            ])
            ->withCount(['weeklyAttendances as total_attended' => fn($q) => $q->where('attended', true)])
            ->get();

        // 2. Fetch members who were newcomers in the selected month
        // (Joined on/before selected month, and were moved to member table LATER)
        $membersAsNewcomers = \App\Models\Member::where('church_id', $church->id)
            ->where(function ($query) use ($monthStart, $monthEnd, $month, $year) {
                // Include if they joined on/before the target month
                $query->where('date_of_visit', '<=', $monthEnd)
                    // AND were only promoted to members AFTER this month started
                    ->where(function ($q) use ($monthStart) {
                    $q->whereNull('membership_approved_at') // Should not happen for members but safety
                        ->orWhere('membership_approved_at', '>=', $monthStart);
                })
                    // OR they already have records in this specific month
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

        // 3. Merge and format for the view
        $allEligible = $newcomers->concat($membersAsNewcomers);

        $groupedAttendance = $allEligible->sortByDesc('date_of_visit')->groupBy(function ($person) {
            return Carbon::parse($person->date_of_visit)->format('F Y');
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
                    'join_date' => Carbon::parse($person->date_of_visit)->format('M d, Y'),
                ];
            });
        });

        return view('admin.attendance.show', compact('church', 'groupedAttendance', 'month', 'year', 'sundays'));
    }

    public function toggle(Request $request)
    {
        $val = $request->validate([
            'id' => 'required',
            'is_member' => 'required|boolean',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'week_number' => 'required|integer',
            'service_date' => 'required|date',
            'attended' => 'required|boolean',
        ]);

        $query = [
            'church_id' => null,
            'month' => $val['month'],
            'year' => $val['year'],
            'week_number' => $val['week_number'],
        ];

        if ($val['is_member']) {
            $person = \App\Models\Member::findOrFail($val['id']);
            $query['member_id'] = $person->id;
            $query['first_timer_id'] = null;
        } else {
            $person = FirstTimer::findOrFail($val['id']);
            $query['first_timer_id'] = $person->id;
            $query['member_id'] = null;
        }

        $query['church_id'] = $person->church_id;

        if ($val['is_member'] || $person->weeklyAttendances()->where('attended', true)->count() >= 6) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance recording is locked for this person.'
            ], 403);
        }

        $attendance = WeeklyAttendance::updateOrCreate(
            [
                'month' => $val['month'],
                'year' => $val['year'],
                'week_number' => $val['week_number'],
                'first_timer_id' => $query['first_timer_id'],
                'member_id' => $query['member_id'],
            ],
            [
                'church_id' => $person->church_id,
                'service_date' => $val['service_date'],
                'attended' => $val['attended'],
                'recorded_by' => auth()->id(),
            ]
        );

        if (!$val['is_member']) {
            $this->firstTimerService->syncMembershipStatus($person);
        }

        return response()->json([
            'success' => true,
            'total_attended' => $person->weeklyAttendances()->where('attended', true)->count(),
            'status' => $val['is_member'] ? 'Retained' : $person->status
        ]);
    }
}
