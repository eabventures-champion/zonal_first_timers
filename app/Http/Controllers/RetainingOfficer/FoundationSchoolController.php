<?php

namespace App\Http\Controllers\RetainingOfficer;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Models\Member;
use App\Models\FoundationClass;
use App\Services\FoundationSchoolService;
use Illuminate\Http\Request;

class FoundationSchoolController extends Controller
{
    public function __construct(private FoundationSchoolService $service)
    {
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $classes = $this->service->getAllClasses();
        $groupedData = collect($this->service->getGroupedProgressData($search, auth()->user()->church_id));
        $totalClassCount = $classes->count();

        return view('retaining-officer.foundation-school.index', compact('classes', 'groupedData', 'totalClassCount', 'search'));
    }

    public function show(Request $request, $id)
    {
        $isMember = $request->query('member') === '1';
        $person = $isMember ? Member::findOrFail($id) : FirstTimer::findOrFail($id);

        if ($person->church_id !== auth()->user()->church_id) {
            abort(403, 'Unauthorized.');
        }

        $person->load(['church', 'foundationAttendances.foundationClass']);
        $progress = $this->service->getStudentProgress($person);
        return view('retaining-officer.foundation-school.show', [
            'firstTimer' => $person,
            'progress' => $progress
        ]);
    }

    public function recordAttendance(Request $request, $id)
    {
        $isMember = $request->query('member') === '1';
        $person = $isMember ? Member::findOrFail($id) : FirstTimer::findOrFail($id);

        if ($person->church_id !== auth()->user()->church_id) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'foundation_class_id' => 'required|exists:foundation_classes,id',
            'attended' => 'required',
            'completed' => 'required',
            'attendance_date' => 'nullable|date',
        ]);

        $this->service->recordAttendance($person, $request->foundation_class_id, [
            'attended' => filter_var($request->attended, FILTER_VALIDATE_BOOLEAN),
            'completed' => filter_var($request->completed, FILTER_VALIDATE_BOOLEAN),
            'attendance_date' => $request->attendance_date
        ]);

        $this->service->checkAndConvert($person);

        return back()->with('success', 'Attendance recorded successfully.');
    }
}
