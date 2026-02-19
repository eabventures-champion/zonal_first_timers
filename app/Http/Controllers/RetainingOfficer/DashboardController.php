<?php

namespace App\Http\Controllers\RetainingOfficer;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\FoundationSchoolService;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private FoundationSchoolService $foundationService
    ) {
    }

    public function index()
    {
        $user = auth()->user();
        $churchId = $user->church_id;

        if (!$churchId) {
            return view('retaining-officer.dashboard', [
                'stats' => ['total_first_timers' => 0, 'new_first_timers' => 0, 'in_progress' => 0, 'total_members' => 0],
                'foundationStats' => [],
                'church' => null,
                'recentFirstTimers' => collect(),
            ]);
        }

        $stats = $this->dashboardService->getChurchStats($churchId);
        $foundationStats = $this->foundationService->getCompletionStatsForChurch($churchId);
        $church = \App\Models\Church::with('group.category')->find($churchId);
        $recentFirstTimers = \App\Models\FirstTimer::where('church_id', $churchId)
            ->latest('date_of_visit')
            ->limit(10)
            ->get();

        return view('retaining-officer.dashboard', compact('stats', 'foundationStats', 'church', 'recentFirstTimers'));
    }
}
