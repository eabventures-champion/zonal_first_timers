<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Services\FirstTimerService;
use Illuminate\Http\Request;

class MembershipApprovalController extends Controller
{
    public function __construct(
        private FirstTimerService $service
    ) {
    }

    public function index()
    {
        $notifications = $this->service->getPendingApprovals();
        return view('admin.membership-approvals.index', compact('notifications'));
    }

    public function approve(\App\Models\Member $member)
    {
        $this->service->acknowledgeMembership($member);
        return back()->with('success', "Notification for {$member->full_name} has been acknowledged.");
    }

    public function bulkAcknowledge(Request $request)
    {
        $memberIds = $request->input('ids', []);
        if (empty($memberIds)) {
            return back()->with('error', 'Please select at least one notification to acknowledge.');
        }

        $count = $this->service->bulkAcknowledge($memberIds);
        return back()->with('success', "Acknowledged {$count} notifications successfully.");
    }

    public function bulkSync()
    {
        $count = $this->service->bulkSyncMembershipStatus();
        return back()->with('success', "Processed {$count} records. Check the pending list for any new candidates.");
    }
}
