<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Models\FoundationClass;
use App\Services\FoundationSchoolService;
use Illuminate\Http\Request;

class FoundationSchoolController extends Controller
{
    public function __construct(private FoundationSchoolService $service)
    {
    }

    public function index()
    {
        $classes = $this->service->getAllClasses();
        return view('admin.foundation-school.index', compact('classes'));
    }

    public function show(FirstTimer $firstTimer)
    {
        $firstTimer->load(['church', 'foundationAttendances.foundationClass']);
        $progress = $this->service->getProgressForFirstTimer($firstTimer);
        return view('admin.foundation-school.show', compact('firstTimer', 'progress'));
    }

    public function recordAttendance(Request $request, FirstTimer $firstTimer)
    {
        $request->validate([
            'foundation_class_id' => 'required|exists:foundation_classes,id',
            'attended' => 'required|boolean',
            'completed' => 'required|boolean',
            'attendance_date' => 'nullable|date',
        ]);

        $this->service->recordAttendance($firstTimer, $request->foundation_class_id, $request->only(['attended', 'completed', 'attendance_date']));

        // Check if all classes are completed → auto-convert to Member
        $converted = $this->service->checkAndConvert($firstTimer);

        $message = 'Attendance recorded successfully.';
        if ($converted) {
            $message .= ' First timer has been converted to Member!';
        }

        return back()->with('success', $message);
    }
}
