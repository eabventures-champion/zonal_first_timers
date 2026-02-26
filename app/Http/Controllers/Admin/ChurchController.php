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
use Illuminate\Http\Request;

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
        $validated = $request->validated();
        $churches = $validated['churches'];
        $groupId = $validated['church_group_id'];
        $officerId = $validated['retaining_officer_id'] ?? null;

        foreach ($churches as $churchData) {
            $this->service->createChurch([
                'church_group_id' => $groupId,
                'retaining_officer_id' => $officerId,
                'name' => $churchData['name'],
                'leader_name' => $churchData['leader_name'] ?? null,
                'leader_contact' => $churchData['leader_contact'] ?? null,
            ]);
        }

        $count = count($churches);
        $message = $count > 1 ? "{$count} churches created successfully." : "Church created successfully.";

        return redirect()->route('admin.churches.index')
            ->with('success', $message);
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

    public function checkLeaderContact(Request $request)
    {
        $contact = $request->contact;
        $excludeId = $request->exclude_id;

        if (!$contact) {
            return response()->json(['exists' => false, 'message' => '']);
        }

        $query = Church::where('leader_contact', $contact);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $church = $query->first();

        return response()->json([
            'exists' => (bool) $church,
            'message' => $church ? "This contact is already assigned to church \"{$church->name}\" (Leader: {$church->leader_name})." : '',
        ]);
    }
}
