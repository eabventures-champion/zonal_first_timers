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
        $pendingApprovals = $this->service->getPendingApprovals();
        return view('admin.membership-approvals.index', compact('pendingApprovals'));
    }

    public function approve(FirstTimer $firstTimer)
    {
        $this->service->approveMembership($firstTimer);
        return back()->with('success', "{$firstTimer->full_name} has been approved as a member.");
    }

    public function bulkSync()
    {
        $count = $this->service->bulkSyncMembershipStatus();
        return back()->with('success', "Processed {$count} records. Check the pending list for any new candidates.");
    }
}
