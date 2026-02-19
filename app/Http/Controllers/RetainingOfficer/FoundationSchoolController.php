<?php

namespace App\Http\Controllers\RetainingOfficer;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Services\FoundationSchoolService;
use Illuminate\Http\Request;

class FoundationSchoolController extends Controller
{
    public function __construct(private FoundationSchoolService $service)
    {
    }

    public function index()
    {
        $churchId = auth()->user()->church_id;
        $classes = $this->service->getAllClasses();
        $totalClassCount = $classes->count();

        // Get first timers for this church who are in progress
        $firstTimers = FirstTimer::where('church_id', $churchId)
            ->whereIn('status', ['New', 'In Progress'])
            ->with('foundationAttendances')
            ->latest()
            ->get();

        return view('retaining-officer.foundation-school.index', compact('classes', 'firstTimers', 'totalClassCount'));
    }

    public function show(FirstTimer $firstTimer)
    {
        if ($firstTimer->church_id !== auth()->user()->church_id) {
            abort(403, 'Unauthorized.');
        }

        $firstTimer->load(['church', 'foundationAttendances.foundationClass']);
        $progress = $this->service->getProgressForFirstTimer($firstTimer);

        return view('retaining-officer.foundation-school.show', compact('firstTimer', 'progress'));
    }

    public function recordAttendance(Request $request, FirstTimer $firstTimer)
    {
        if ($firstTimer->church_id !== auth()->user()->church_id) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'foundation_class_id' => 'required|exists:foundation_classes,id',
            'attended' => 'required|boolean',
            'completed' => 'required|boolean',
            'attendance_date' => 'nullable|date',
        ]);

        $this->service->recordAttendance($firstTimer, $request->foundation_class_id, $request->only(['attended', 'completed', 'attendance_date']));
        $converted = $this->service->checkAndConvert($firstTimer);

        $message = 'Attendance recorded successfully.';
        if ($converted) {
            $message .= ' First timer has been converted to Member!';
        }

        return back()->with('success', $message);
    }
}
