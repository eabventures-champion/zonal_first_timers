<?php

namespace App\Http\Controllers\RetainingOfficer;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Services\FirstTimerService;
use App\Services\FoundationSchoolService;
use Illuminate\Http\Request;

class FirstTimerController extends Controller
{
    public function __construct(
        private FirstTimerService $service,
        private FoundationSchoolService $foundationService
    ) {
    }

    public function index(Request $request)
    {
        $churchId = auth()->user()->church_id;
        $filters = $request->only(['status', 'search', 'date_from', 'date_to']);
        $firstTimers = $this->service->getForChurch($churchId, $filters);

        return view('retaining-officer.first-timers.index', compact('firstTimers', 'filters'));
    }

    public function show(FirstTimer $firstTimer)
    {
        // Scope check
        if ($firstTimer->church_id !== auth()->user()->church_id) {
            abort(403, 'Unauthorized: This first timer does not belong to your church.');
        }

        $firstTimer->load(['church', 'retainingOfficer', 'weeklyAttendances', 'foundationAttendances.foundationClass']);
        $foundationProgress = $this->foundationService->getStudentProgress($firstTimer);

        return view('retaining-officer.first-timers.show', compact('firstTimer', 'foundationProgress'));
    }
}
