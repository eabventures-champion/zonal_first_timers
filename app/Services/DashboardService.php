<?php

namespace App\Services;

use App\Models\Church;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\FirstTimer;
use App\Models\Member;
use App\Models\FoundationClass;
use App\Models\FoundationAttendance;
use App\Models\WeeklyAttendance;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get admin/super-admin global overview
     */
    public function getGlobalStats(): array
    {
        return [
            'total_churches' => Church::count(),
            'total_categories' => ChurchCategory::count(),
            'total_groups' => ChurchGroup::count(),
            'total_first_timers' => FirstTimer::count() + Member::count(),
            'new_first_timers' => FirstTimer::where('status', 'New')->count(),
            'developing' => FirstTimer::where('status', 'Developing')->count(),
            'total_members' => Member::count(),
            'total_retaining_officers' => User::role('Retaining Officer')->count(),
            'pending_approvals' => Member::whereNull('acknowledged_at')->count(),
            'monthly_target' => (int) Setting::get('monthly_registration_target', 50),
        ];
    }

    /**
     * Get gender distribution for charts
     */
    public function getGenderDistribution(?int $churchId = null): array
    {
        if ($churchId) {
            $counts = FirstTimer::where('church_id', $churchId)
                ->select('gender', DB::raw('count(*) as count'))
                ->groupBy('gender')
                ->pluck('count', 'gender')
                ->toArray();

            $total = array_sum($counts);
            return [
                'Church Overview' => [
                    'data' => $counts,
                    'total' => $total
                ]
            ];
        }

        // Global view - breakdown by category
        $distribution = [];

        // 1. Overall Distribution
        $overallCounts = FirstTimer::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();
        $overallTotal = array_sum($overallCounts);

        if ($overallTotal > 0) {
            $distribution['Overall Total'] = [
                'data' => $overallCounts,
                'total' => $overallTotal
            ];
        }

        // 2. Per Category Distribution
        $categories = ChurchCategory::withCount('churches')->get();

        foreach ($categories as $category) {
            $categoryCounts = FirstTimer::whereHas('church.group', function ($q) use ($category) {
                $q->where('church_category_id', $category->id);
            })
                ->select('gender', DB::raw('count(*) as count'))
                ->groupBy('gender')
                ->pluck('count', 'gender')
                ->toArray();

            $categoryTotal = array_sum($categoryCounts);

            if ($categoryTotal > 0) {
                $distribution[$category->name] = [
                    'data' => $categoryCounts,
                    'total' => $categoryTotal
                ];
            }
        }

        return $distribution;
    }

    /**
     * Get monthly trend (first timers per month) breakdown by church categories
     */
    public function getMonthlyTrend(?int $churchId = null, string $period = 'last_6_months'): array
    {
        $query = FirstTimer::query()
            ->join('churches', 'first_timers.church_id', '=', 'churches.id')
            ->join('church_groups', 'churches.church_group_id', '=', 'church_groups.id')
            ->join('church_categories', 'church_groups.church_category_id', '=', 'church_categories.id')
            ->select(
                DB::raw("DATE_FORMAT(date_of_visit, '%Y-%m') as month"),
                'church_categories.name as category_name',
                DB::raw('count(*) as count')
            );

        if ($churchId) {
            $query->where('first_timers.church_id', $churchId);
        }

        switch ($period) {
            case 'this_year':
                $query->whereYear('date_of_visit', now()->year);
                break;
            case 'last_year':
                $query->whereYear('date_of_visit', now()->subYear()->year);
                break;
            case 'last_6_months':
            default:
                $query->where('date_of_visit', '>=', now()->subMonths(6));
                break;
        }

        $results = $query->groupBy('month', 'category_name')
            ->orderBy('month')
            ->get();

        // Determine start date based on period
        $startDate = now();
        $count = 6;
        switch ($period) {
            case 'this_month':
                $startDate = now()->startOfMonth();
                $count = 1;
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                $count = now()->month;
                break;
            case 'last_year':
                $startDate = now()->subYear()->startOfYear();
                $count = 12;
                break;
            case 'last_6_months':
            default:
                $startDate = now()->subMonths(5)->startOfMonth();
                $count = 6;
                break;
        }

        // Generate all months in range
        $months = [];
        for ($i = 0; $i < $count; $i++) {
            $months[] = $startDate->copy()->addMonths($i)->format('Y-m');
        }

        // Get all unique categories from results OR from DB to ensure consistency
        $categories = $results->pluck('category_name')->unique()->toArray();
        if (empty($categories)) {
            $categories = ChurchCategory::pluck('name')->toArray();
        }

        $series = [];
        foreach ($categories as $catName) {
            $data = [];
            foreach ($months as $month) {
                $match = $results->where('month', $month)->where('category_name', $catName)->first();
                $data[] = $match ? $match->count : 0;
            }
            $series[] = [
                'name' => $catName,
                'data' => $data
            ];
        }

        // Map month keys to short names (Nov, Dec...)
        $monthNames = array_map(fn($m) => date('M', strtotime($m . '-01')), $months);

        return [
            'labels' => $monthNames,
            'series' => $series,
            'months' => $months
        ];
    }

    /**
     * Get church-specific dashboard stats
     */
    public function getChurchStats(int $churchId): array
    {
        $totalFirstTimers = FirstTimer::where('church_id', $churchId)->count();
        $newCount = FirstTimer::where('church_id', $churchId)->where('status', 'New')->count();
        $developingCount = FirstTimer::where('church_id', $churchId)->where('status', 'Developing')->count();
        $memberCount = Member::where('church_id', $churchId)->count();
        $totalBringers = \App\Models\Bringer::where('church_id', $churchId)
            ->where(function ($q) {
                $q->has('firstTimers')->orHas('members');
            })
            ->count();

        $totalFirstTimers = FirstTimer::where('church_id', $churchId)->count() + $memberCount;

        $retentionRate = $totalFirstTimers > 0
            ? round(($memberCount / $totalFirstTimers) * 100, 1)
            : 0;

        // Foundation school stats
        $totalClasses = FoundationClass::count();
        $firstTimerIds = FirstTimer::where('church_id', $churchId)->pluck('id');

        $completedFoundation = 0;
        if ($totalClasses > 0) {
            $completedFoundation = FirstTimer::where('church_id', $churchId)
                ->whereHas('foundationAttendances', function ($q) {
                    $q->where('completed', true);
                }, '>=', $totalClasses)
                ->count();
        }

        $foundationRate = $totalFirstTimers > 0
            ? round(($completedFoundation / $totalFirstTimers) * 100, 1)
            : 0;

        return [
            'total_first_timers' => $totalFirstTimers,
            'new_first_timers' => $newCount,
            'developing' => $developingCount,
            'total_members' => $memberCount,
            'total_bringers' => $totalBringers,
            'retention_rate' => $retentionRate,
            'foundation_completion_rate' => $foundationRate,
            'gender_distribution' => $this->getGenderDistribution($churchId),
        ];
    }

    /**
     * Per-church performance data for admin dashboard
     */
    public function getChurchPerformance(): array
    {
        $categories = ChurchCategory::with([
            'groups.churches.retainingOfficer',
            'groups.churches' => function ($q) {
                $q->withStats();
            }
        ])->get();

        return $categories->map(function ($category) {
            $categoryGroups = $category->groups->map(function ($group) {
                $churches = $group->churches->map(function ($church) {
                    $totalSouls = $church->first_timers_count + $church->members_count;
                    $retentionRate = $totalSouls > 0
                        ? round(($church->members_count / $totalSouls) * 100, 1)
                        : 0;

                    return [
                        'id' => $church->id,
                        'name' => $church->name,
                        'retaining_officer' => $church->retainingOfficer->name ?? 'Unassigned',
                        'total_first_timers' => $totalSouls,
                        'new' => $church->new_first_timers_count,
                        'developing' => $church->developing_count,
                        'members' => $church->members_count,
                        'retention_rate' => $retentionRate,
                    ];
                })->sortByDesc('retention_rate')->values();

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'churches' => $churches,
                    'total_retention' => round($churches->avg('retention_rate') ?? 0),
                ];
            })->filter(fn($g) => $g['churches']->isNotEmpty())->values();

            return [
                'id' => $category->id,
                'name' => $category->name,
                'groups' => $categoryGroups,
                'total_churches' => $categoryGroups->sum(fn($g) => $g['churches']->count()),
                'total_retention' => round($categoryGroups->avg('total_retention') ?? 0),
            ];
        })->filter(fn($c) => $c['groups']->isNotEmpty())->values()->toArray();
    }

    /**
     * Weekly attendance trend for a church (last 6 weeks)
     */
    public function getWeeklyAttendanceTrend(int $churchId): array
    {
        return WeeklyAttendance::where('church_id', $churchId)
            ->where('service_date', '>=', now()->subWeeks(6))
            ->select(
                'week_number',
                DB::raw('SUM(CASE WHEN attended = 1 THEN 1 ELSE 0 END) as attended_count'),
                DB::raw('count(*) as total')
            )
            ->groupBy('week_number')
            ->orderBy('week_number')
            ->get()
            ->toArray();
    }
    /**
     * Get upcoming birthdays (current month)
     */
    public function getUpcomingBirthdays(?int $churchId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = FirstTimer::query()
            ->whereMonth('date_of_birth', now()->month)
            ->orderByRaw('DAY(date_of_birth)');

        if ($churchId) {
            $query->where('church_id', $churchId);
        }

        return $query->limit(30)->get();
    }

    /**
     * Get upcoming birthdays grouped by church group (for admin dashboard)
     * Returns birthdays from this month + next 30 days, categorized by group.
     */
    public function getUpcomingBirthdaysGrouped(): \Illuminate\Support\Collection
    {
        $today = \Carbon\Carbon::today();

        // Gather first timers with church.group
        $firstTimers = FirstTimer::with('church.group')
            ->whereNotNull('date_of_birth')
            ->get()
            ->map(fn($p) => (object) [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'date_of_birth' => $p->date_of_birth,
                'primary_contact' => $p->primary_contact,
                'type' => 'First Timer',
                'status' => $p->status,
                'church_name' => $p->church->name ?? 'N/A',
                'group_name' => $p->church->group->name ?? 'Ungrouped',
            ]);

        // Gather members with church.group
        $members = Member::with('church.group')
            ->whereNotNull('date_of_birth')
            ->get()
            ->map(fn($p) => (object) [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'date_of_birth' => $p->date_of_birth,
                'primary_contact' => $p->primary_contact,
                'type' => 'Member',
                'status' => $p->status ?? 'Retained',
                'church_name' => $p->church->name ?? 'N/A',
                'group_name' => $p->church->group->name ?? 'Ungrouped',
            ]);

        // Combine + compute days_until
        $all = $firstTimers->concat($members)
            ->map(function ($person) use ($today) {
                $dob = \Carbon\Carbon::parse($person->date_of_birth);
                $birthday = $dob->copy()->year($today->year);

                $diff = $today->diffInDays($birthday, false);

                $isCurrentMonth = $birthday->month === $today->month && $birthday->year === $today->year;

                if ($diff < 0 && !$isCurrentMonth) {
                    $birthday->addYear();
                    $diff = $today->diffInDays($birthday, false);
                }

                $person->days_until = (int) $diff;
                $person->birthday_date = $birthday;
                $person->already_passed = $diff < 0;
                return $person;
            })
            ->filter(fn($p) => $p->days_until <= 30 && $p->days_until >= -31)
            ->filter(fn($p) => $p->already_passed || $p->days_until <= 30)
            ->sortBy('days_until')
            ->values();

        // Group by group_name
        return $all->groupBy('group_name')->map(fn($items) => $items->values());
    }
}
