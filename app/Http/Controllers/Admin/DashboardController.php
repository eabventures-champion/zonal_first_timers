<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service)
    {
    }

    public function index(Request $request)
    {
        $period = $request->get('trend_period', 'last_6_months');
        $stats = $this->service->getGlobalStats();
        $genderDistribution = $this->service->getGenderDistribution();
        $monthlyTrend = $this->service->getMonthlyTrend(null, $period);
        $churchPerformance = $this->service->getChurchPerformance();
        $upcomingBirthdays = $this->service->getUpcomingBirthdays();

        return view('admin.dashboard', compact(
            'stats',
            'genderDistribution',
            'monthlyTrend',
            'churchPerformance',
            'upcomingBirthdays'
        ));
    }

    public function updateTarget(Request $request)
    {
        $request->validate([
            'target' => 'required|integer|min:1'
        ]);

        \App\Models\Setting::set('monthly_registration_target', $request->target, 'dashboard');

        return back()->with('success', 'Monthly registration target updated successfully.');
    }
}
