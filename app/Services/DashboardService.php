<?php

namespace App\Services;

use App\Models\Church;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\FirstTimer;
use App\Models\FoundationClass;
use App\Models\FoundationAttendance;
use App\Models\WeeklyAttendance;
use App\Models\User;
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
            'total_first_timers' => FirstTimer::count(),
            'new_first_timers' => FirstTimer::where('status', 'New')->count(),
            'in_progress' => FirstTimer::where('status', 'In Progress')->count(),
            'total_members' => FirstTimer::where('status', 'Member')->count(),
            'total_retaining_officers' => User::role('Retaining Officer')->count(),
        ];
    }

    /**
     * Get gender distribution for charts
     */
    public function getGenderDistribution(?int $churchId = null): array
    {
        $query = FirstTimer::query();
        if ($churchId) {
            $query->where('church_id', $churchId);
        }

        return $query->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();
    }

    /**
     * Get monthly trend (first timers per month)
     */
    public function getMonthlyTrend(?int $churchId = null, int $months = 6): array
    {
        $query = FirstTimer::select(
            DB::raw("DATE_FORMAT(date_of_visit, '%Y-%m') as month"),
            DB::raw('count(*) as count')
        );

        if ($churchId) {
            $query->where('church_id', $churchId);
        }

        return $query->where('date_of_visit', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }

    /**
     * Get church-specific dashboard stats
     */
    public function getChurchStats(int $churchId): array
    {
        $totalFirstTimers = FirstTimer::where('church_id', $churchId)->count();
        $newCount = FirstTimer::where('church_id', $churchId)->where('status', 'New')->count();
        $inProgressCount = FirstTimer::where('church_id', $churchId)->where('status', 'In Progress')->count();
        $memberCount = FirstTimer::where('church_id', $churchId)->where('status', 'Member')->count();

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
            'in_progress' => $inProgressCount,
            'total_members' => $memberCount,
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
        return Church::with(['group.category', 'retainingOfficer'])
            ->withStats()
            ->get()
            ->map(function ($church) {
                $retentionRate = $church->first_timers_count > 0
                    ? round(($church->members_count / $church->first_timers_count) * 100, 1)
                    : 0;

                return [
                    'id' => $church->id,
                    'name' => $church->name,
                    'category' => $church->group->category->name ?? 'N/A',
                    'group' => $church->group->name ?? 'N/A',
                    'retaining_officer' => $church->retainingOfficer->name ?? 'Unassigned',
                    'total_first_timers' => $church->first_timers_count,
                    'new' => $church->new_first_timers_count,
                    'in_progress' => $church->in_progress_count,
                    'members' => $church->members_count,
                    'retention_rate' => $retentionRate,
                ];
            })->toArray();
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
}
