<?php

namespace App\Http\Controllers\RetainingOfficer;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Models\Member;
use App\Services\DashboardService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
    }

    public function index()
    {
        $user = auth()->user();
        $churchId = $user->church_id;

        if (!$churchId) {
            return view('retaining-officer.dashboard', [
                'stats' => ['total_first_timers' => 0, 'new_first_timers' => 0, 'developing' => 0, 'total_members' => 0],
                'upcomingBirthdays' => collect(),
                'church' => null,
                'recentFirstTimers' => collect(),
            ]);
        }

        $stats = $this->dashboardService->getChurchStats($churchId);
        $church = \App\Models\Church::with('group.category')->find($churchId);
        $recentFirstTimers = FirstTimer::where('church_id', $churchId)
            ->latest('date_of_visit')
            ->limit(10)
            ->get();

        // Upcoming birthdays (next 30 days) for first timers & members in this church
        $today = Carbon::today();
        $upcomingBirthdays = collect();

        $firstTimers = FirstTimer::where('church_id', $churchId)
            ->whereNotNull('date_of_birth')
            ->get()
            ->map(fn($p) => (object) [
                'full_name' => $p->full_name,
                'date_of_birth' => $p->date_of_birth,
                'type' => 'First Timer',
                'status' => $p->status,
            ]);

        $members = Member::where('church_id', $churchId)
            ->whereNotNull('date_of_birth')
            ->get()
            ->map(fn($p) => (object) [
                'full_name' => $p->full_name,
                'date_of_birth' => $p->date_of_birth,
                'type' => 'Member',
                'status' => $p->status,
            ]);

        $upcomingBirthdays = $firstTimers->concat($members)
            ->map(function ($person) use ($today) {
                $dob = Carbon::parse($person->date_of_birth);
                $birthday = $dob->copy()->year($today->year);

                // Calculate days difference (negative = already passed this year)
                $diff = $today->diffInDays($birthday, false);

                // If the birthday already passed this year but is still in the current month, show it
                $startOfMonth = $today->copy()->startOfMonth();
                $isCurrentMonth = $birthday->month === $today->month && $birthday->year === $today->year;

                if ($diff < 0 && !$isCurrentMonth) {
                    // Birthday passed and not in the current month — push to next year
                    $birthday->addYear();
                    $diff = $today->diffInDays($birthday, false);
                }

                $person->days_until = (int) $diff;
                $person->birthday_date = $birthday;
                $person->already_passed = $diff < 0;
                return $person;
            })
            ->filter(fn($p) => $p->days_until <= 30 && $p->days_until >= -31) // current month past + 30 days future
            ->filter(fn($p) => $p->already_passed || $p->days_until <= 30)
            ->sortBy('days_until')
            ->values();

        return view('retaining-officer.dashboard', compact('stats', 'upcomingBirthdays', 'church', 'recentFirstTimers'));
    }
}
