<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFirstTimerRequest;
use App\Http\Requests\UpdateFirstTimerRequest;
use App\Http\Requests\ImportFirstTimersRequest;
use App\Models\Church;
use App\Models\FirstTimer;
use App\Models\User;
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
        $filters = $request->only(['church_id', 'status', 'search', 'date_from', 'date_to']);
        $firstTimers = $this->service->getAll($filters);
        $churches = Church::all();
        return view('admin.first-timers.index', compact('firstTimers', 'churches', 'filters'));
    }

    public function create()
    {
        $churches = Church::with('retainingOfficer')->get();
        $officers = User::role('Retaining Officer')->get();
        return view('admin.first-timers.create', compact('churches', 'officers'));
    }

    public function store(StoreFirstTimerRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('admin.first-timers.index')
            ->with('success', 'First timer registered successfully.');
    }

    public function show(FirstTimer $firstTimer)
    {
        $firstTimer->load(['church.group.category', 'retainingOfficer', 'weeklyAttendances', 'foundationAttendances.foundationClass']);
        $foundationProgress = $this->foundationService->getProgressForFirstTimer($firstTimer);
        return view('admin.first-timers.show', compact('firstTimer', 'foundationProgress'));
    }

    public function edit(FirstTimer $firstTimer)
    {
        if ($firstTimer->status === 'Member') {
            return redirect()->route('admin.first-timers.show', $firstTimer)
                ->with('error', 'Member records are read-only.');
        }

        $churches = Church::with('retainingOfficer')->get();
        $officers = User::role('Retaining Officer')->get();
        return view('admin.first-timers.edit', compact('firstTimer', 'churches', 'officers'));
    }

    public function update(UpdateFirstTimerRequest $request, FirstTimer $firstTimer)
    {
        try {
            $this->service->update($firstTimer, $request->validated());
            return redirect()->route('admin.first-timers.index')
                ->with('success', 'First timer updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(FirstTimer $firstTimer)
    {
        $this->service->delete($firstTimer);
        return redirect()->route('admin.first-timers.index')
            ->with('success', 'First timer deleted successfully.');
    }

    // ── CSV Import ─────────────────────────────────────────

    public function importForm()
    {
        $churches = Church::all();
        return view('admin.first-timers.import', compact('churches'));
    }

    public function import(ImportFirstTimersRequest $request)
    {
        $results = $this->service->importFromCsv(
            $request->file('csv_file'),
            $request->church_id
        );

        $message = "{$results['success']} first timers imported successfully.";
        if (!empty($results['errors'])) {
            $message .= ' ' . count($results['errors']) . ' errors encountered.';
        }

        return redirect()->route('admin.first-timers.index')
            ->with('success', $message)
            ->with('import_errors', $results['errors']);
    }
}
