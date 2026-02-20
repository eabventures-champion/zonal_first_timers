<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Church;
use App\Models\Member;
use App\Models\User;
use App\Services\FirstTimerService;
use App\Services\FoundationSchoolService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(
        private FirstTimerService $service,
        private FoundationSchoolService $foundationService
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['church_id', 'search', 'date_from', 'date_to']);
        $members = $this->service->getMembers($filters, false); // Fetch all for grouped view
        $churches = Church::all();

        // Group by group name for structured view
        $groupedMembers = $members->groupBy(function ($m) {
            return $m->church->group->name ?? 'Unassigned Groups';
        });

        return view('admin.members.index', compact('groupedMembers', 'churches', 'filters'));
    }

    public function show(Member $member)
    {
        $member->load(['church.group.category', 'retainingOfficer', 'weeklyAttendances', 'foundationAttendances.foundationClass']);
        $foundationProgress = $this->foundationService->getStudentProgress($member);
        return view('admin.members.show', compact('member', 'foundationProgress'));
    }

    public function edit(Member $member)
    {
        $member->load('church.group.category');
        $categories = \App\Models\ChurchCategory::with([
            'groups.churches' => function ($q) {
                $q->with('retainingOfficer')->orderBy('name');
            }
        ])->orderBy('name')->get();

        $officers = User::role('Retaining Officer')->get();
        return view('admin.members.edit', compact('member', 'categories', 'officers'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->except(['_token', '_method', 'dob_day', 'dob_month']);

        // Handle booleans for checkboxes (unchecked checkboxes aren't sent in the request)
        $data['born_again'] = $request->boolean('born_again');
        $data['water_baptism'] = $request->boolean('water_baptism');

        if ($request->filled('dob_day') && $request->filled('dob_month')) {
            $data['date_of_birth'] = "2000-{$request->dob_month}-{$request->dob_day}";
        }

        \Log::info("Updating member ID {$member->id}", [
            'raw_request' => $request->all(),
            'processed_data' => $data
        ]);

        $member->update($data);

        return redirect()->route('admin.members.index')
            ->with('success', 'Member updated successfully.');
    }
}
