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
        ])
            ->orderByRaw("CASE WHEN name IN ('AVENOR', 'LAA') THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

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
                // If this Sunday is in the future, don't show the week
                if ($currentDate->isFuture() && !$currentDate->isToday()) {
                    continue;
                }

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
        // We show newcomers for this church who haven't been approved as members yet
        $newcomers = FirstTimer::where('church_id', $church->id)
            ->whereNull('membership_approved_at')
            ->where(function ($query) use ($monthStart, $monthEnd, $month, $year) {
                // Include if they joined on/before the target month
                $query->where('date_of_visit', '<=', $monthEnd)
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
            return $group->sortByDesc('id')->map(function ($person) {
                $weeks = [];
                foreach ($person->weeklyAttendances->sortBy('service_date') as $wa) {
                    $weeks[$wa->week_number][] = [
                        'id' => $wa->id,
                        'status' => $wa->attended ? 'attended' : 'absent',
                        'service_date' => $wa->service_date,
                        'formatted_date' => Carbon::parse($wa->service_date)->format('D d'),
                    ];
                }

                $isMember = get_class($person) === \App\Models\Member::class;

                return [
                    'id' => $person->id,
                    'row_id' => ($isMember ? 'm_' : 'ft_') . $person->id,
                    'name' => $person->full_name,
                    'is_member' => $isMember,
                    'is_readonly' => ($person->total_attended >= 6),
                    'total_attended' => $person->total_attended,
                    'weeks' => $weeks,
                    'join_date' => Carbon::parse($person->date_of_visit)->format('M d, Y'),
                ];
            })->values(); // Reset array keys sequentially for Alpine/Blade to avoid weird index tracking bugs
        });

        return view('admin.attendance.show', compact('church', 'groupedAttendance', 'month', 'year', 'sundays'));
    }

    public function toggle(Request $request)
    {
        \Log::info('Admin Attendance Toggle Request:', $request->all());

        $val = $request->validate([
            'id' => 'required',
            'is_member' => 'required|boolean',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'week_number' => 'required|integer',
            'service_date' => 'required|date',
            'status' => 'required|in:attended,absent,clear',
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

        $attendedCount = $person->weeklyAttendances()->where('attended', true)->count();
        if ($attendedCount >= 6) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance recording is locked for this person.'
            ], 403);
        }

        if ($val['status'] === 'clear') {
            WeeklyAttendance::where([
                'month' => $val['month'],
                'year' => $val['year'],
                'week_number' => $val['week_number'],
                'first_timer_id' => $query['first_timer_id'],
                'member_id' => $query['member_id'],
            ])->whereDate('service_date', $val['service_date'])->forceDelete();
        } else {
            // Check for a soft-deleted record first and restore it
            $trashed = WeeklyAttendance::withTrashed()->where([
                'month' => $val['month'],
                'year' => $val['year'],
                'week_number' => $val['week_number'],
                'first_timer_id' => $query['first_timer_id'],
                'member_id' => $query['member_id'],
            ])->whereDate('service_date', $val['service_date'])->first();

            if ($trashed && $trashed->trashed()) {
                $trashed->restore();
                $trashed->update([
                    'church_id' => $person->church_id,
                    'attended' => $val['status'] === 'attended',
                    'notes' => null,
                    'recorded_by' => auth()->id(),
                ]);
            } else {
                WeeklyAttendance::updateOrCreate(
                    [
                        'month' => $val['month'],
                        'year' => $val['year'],
                        'week_number' => $val['week_number'],
                        'service_date' => $val['service_date'],
                        'first_timer_id' => $query['first_timer_id'],
                        'member_id' => $query['member_id'],
                    ],
                    [
                        'church_id' => $person->church_id,
                        'attended' => $val['status'] === 'attended',
                        'recorded_by' => auth()->id(),
                    ]
                );
            }
        }

        // Update the date_of_visit (join date) to be the earliest recorded attendance date
        $earliestAttendance = WeeklyAttendance::where(function ($q) use ($query) {
            if ($query['member_id']) {
                $q->where('member_id', $query['member_id']);
            } else {
                $q->where('first_timer_id', $query['first_timer_id']);
            }
        })
            ->where('attended', true)
            ->orderBy('service_date', 'asc')
            ->first();

        if ($earliestAttendance) {
            $earliestDate = Carbon::parse($earliestAttendance->service_date)->startOfDay();
            $currentJoinDate = Carbon::parse($person->date_of_visit)->startOfDay();

            // Always enforce that the date of visit matches the earliest attendance date
            if ($earliestDate->notEqualTo($currentJoinDate)) {
                $person->update(['date_of_visit' => $earliestDate->toDateString()]);
            }
        } else {
            // No attendance records exist OR they are all marked 'absent'; default back to original visit date logic
            $createdAt = Carbon::parse($person->created_at)->startOfDay();
            if ($createdAt->notEqualTo(Carbon::parse($person->date_of_visit)->startOfDay())) {
                $person->update(['date_of_visit' => $createdAt->toDateString()]);
            }
        }

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
