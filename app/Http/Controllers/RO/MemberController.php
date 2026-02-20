<?php

namespace App\Http\Controllers\RO;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\FirstTimerService;
use App\Services\FoundationSchoolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function __construct(
        private FirstTimerService $service,
        private FoundationSchoolService $foundationService
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'date_from', 'date_to']);
        $filters['church_id'] = Auth::user()->church_id;
        $members = $this->service->getMembers($filters);
        return view('ro.members.index', compact('members', 'filters'));
    }

    public function show(Member $member)
    {
        // Scope check — only allow viewing members from the officer's church
        if ($member->church_id !== Auth::user()->church_id) {
            abort(403, 'Unauthorized: This member does not belong to your church.');
        }

        $member->load(['church.group.category', 'retainingOfficer', 'weeklyAttendances', 'foundationAttendances.foundationClass']);
        $foundationProgress = $this->foundationService->getStudentProgress($member);

        return view('ro.members.show', compact('member', 'foundationProgress'));
    }
}
