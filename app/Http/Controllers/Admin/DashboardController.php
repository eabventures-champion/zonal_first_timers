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

    public function index()
    {
        $stats = $this->service->getGlobalStats();
        $genderDistribution = $this->service->getGenderDistribution();
        $monthlyTrend = $this->service->getMonthlyTrend();
        $churchPerformance = $this->service->getChurchPerformance();

        return view('admin.dashboard', compact(
            'stats',
            'genderDistribution',
            'monthlyTrend',
            'churchPerformance'
        ));
    }
}
