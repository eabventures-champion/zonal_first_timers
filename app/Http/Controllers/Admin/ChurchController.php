<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChurchRequest;
use App\Http\Requests\UpdateChurchRequest;
use App\Models\Church;
use App\Models\ChurchGroup;
use App\Models\User;
use App\Services\ChurchHierarchyService;
use App\Services\DashboardService;

class ChurchController extends Controller
{
    public function __construct(
        private ChurchHierarchyService $service,
        private DashboardService $dashboardService
    ) {
    }

    public function index()
    {
        $groups = $this->service->getAllGroupsWithChurches();
        return view('admin.churches.index', compact('groups'));
    }

    public function create()
    {
        $groups = ChurchGroup::with('category')->get();
        $officers = User::role('Retaining Officer')->with('church')->get();
        return view('admin.churches.create', compact('groups', 'officers'));
    }

    public function store(StoreChurchRequest $request)
    {
        $this->service->createChurch($request->validated());
        return redirect()->route('admin.churches.index')
            ->with('success', 'Church created successfully.');
    }

    public function show(Church $church)
    {
        $church->load(['group.category', 'retainingOfficer', 'firstTimers']);
        $stats = $this->dashboardService->getChurchStats($church->id);
        $attendanceTrend = $this->dashboardService->getWeeklyAttendanceTrend($church->id);
        return view('admin.churches.show', compact('church', 'stats', 'attendanceTrend'));
    }

    public function edit(Church $church)
    {
        $groups = ChurchGroup::with('category')->get();
        $officers = User::role('Retaining Officer')->with('church')->get();
        return view('admin.churches.edit', compact('church', 'groups', 'officers'));
    }

    public function update(UpdateChurchRequest $request, Church $church)
    {
        $this->service->updateChurch($church, $request->validated());
        return redirect()->route('admin.churches.index')
            ->with('success', 'Church updated successfully.');
    }

    public function destroy(Church $church)
    {
        $this->service->deleteChurch($church);
        return redirect()->route('admin.churches.index')
            ->with('success', 'Church deleted successfully.');
    }
}
