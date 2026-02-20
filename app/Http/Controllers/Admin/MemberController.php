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
        $member->update($request->only([
            'full_name',
            'primary_contact',
            'alternate_contact',
            'gender',
            'date_of_birth',
            'age',
            'residential_address',
            'occupation',
            'marital_status',
            'email',
            'church_id',
            'retaining_officer_id',
        ]));

        return redirect()->route('admin.members.index')
            ->with('success', 'Member updated successfully.');
    }
}
