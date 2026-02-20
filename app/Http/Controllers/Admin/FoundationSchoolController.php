<?php

namespace App\Http\Controllers\Admin;

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
        $hierarchicalData = $this->service->getHierarchicalProgressData($search);

        $totalInProgress = collect($hierarchicalData)->sum('total_students');

        return view('admin.foundation-school.index', compact('classes', 'hierarchicalData', 'totalInProgress', 'search'));
    }

    public function show(Request $request, $id)
    {
        $isMember = $request->query('member') === '1';
        $person = $isMember ? Member::findOrFail($id) : FirstTimer::findOrFail($id);
        $person->load(['church', 'foundationAttendances.foundationClass']);

        $progress = $this->service->getStudentProgress($person);

        return view('admin.foundation-school.show', [
            'firstTimer' => $person,
            'progress' => $progress
        ]);
    }

    public function recordAttendance(Request $request, $id)
    {
        $isMember = $request->query('member') === '1';
        $person = $isMember ? Member::findOrFail($id) : FirstTimer::findOrFail($id);

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
    public function updateClass(Request $request, FoundationClass $class)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $class->update($request->only(['name', 'description']));

        return back()->with('success', 'Class updated successfully.');
    }
}
