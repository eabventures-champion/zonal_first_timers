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

    public function create()
    {
        if (!auth()->user()->isOtherChurchRO()) {
            abort(403, 'Unauthorized: Only Retaining Officers from "Other Churches" can register first timers.');
        }

        return view('retaining-officer.first-timers.create');
    }

    public function store(\App\Http\Requests\StoreFirstTimerRequest $request)
    {
        if (!auth()->user()->isOtherChurchRO()) {
            abort(403);
        }

        $data = $request->validated();
        $data['church_id'] = auth()->user()->church_id;

        $this->service->create($data);

        return redirect()->route('ro.first-timers.index')
            ->with('success', 'First timer registered successfully.');
    }

    public function importForm()
    {
        if (!auth()->user()->isOtherChurchRO()) {
            abort(403);
        }

        return view('retaining-officer.first-timers.import');
    }

    public function import(\App\Http\Requests\ImportFirstTimersRequest $request)
    {
        if (!auth()->user()->isOtherChurchRO()) {
            abort(403);
        }

        $results = $this->service->importFromCsv(
            $request->file('csv_file'),
            auth()->user()->church_id
        );

        $message = "{$results['success']} first timers imported successfully.";
        if (!empty($results['errors'])) {
            $message .= ' ' . count($results['errors']) . ' errors encountered.';
        }

        return redirect()->route('ro.first-timers.index')
            ->with('success', $message)
            ->with('import_errors', $results['errors']);
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
