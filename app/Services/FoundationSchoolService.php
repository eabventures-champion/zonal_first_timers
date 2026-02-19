<?php

namespace App\Services;

use App\Models\FirstTimer;
use App\Models\FoundationAttendance;
use App\Models\FoundationClass;
use Illuminate\Support\Facades\Auth;

class FoundationSchoolService
{
    public function getAllClasses()
    {
        return FoundationClass::ordered()->get();
    }

    public function getProgressForFirstTimer(FirstTimer $firstTimer)
    {
        $classes = FoundationClass::ordered()->get();

        return $classes->map(function ($class) use ($firstTimer) {
            $attendance = FoundationAttendance::where('first_timer_id', $firstTimer->id)
                ->where('foundation_class_id', $class->id)
                ->first();

            return [
                'class' => $class,
                'attendance' => $attendance,
                'attended' => $attendance?->attended ?? false,
                'completed' => $attendance?->completed ?? false,
            ];
        });
    }

    public function recordAttendance(FirstTimer $firstTimer, int $classId, array $data): FoundationAttendance
    {
        return FoundationAttendance::updateOrCreate(
            [
                'first_timer_id' => $firstTimer->id,
                'foundation_class_id' => $classId,
            ],
            [
                'attended' => $data['attended'] ?? true,
                'completed' => $data['completed'] ?? false,
                'attendance_date' => $data['attendance_date'] ?? now(),
            ]
        );
    }

    public function checkAndConvert(FirstTimer $firstTimer): bool
    {
        $totalClasses = FoundationClass::count();
        $completedClasses = FoundationAttendance::where('first_timer_id', $firstTimer->id)
            ->where('completed', true)
            ->count();

        if ($completedClasses >= $totalClasses && $totalClasses > 0) {
            $firstTimer->update([
                'status' => 'Member',
                'updated_by' => Auth::id(),
            ]);
            return true;
        }

        // If in progress, update status
        if ($completedClasses > 0 && $firstTimer->status === 'New') {
            $firstTimer->update([
                'status' => 'In Progress',
                'updated_by' => Auth::id(),
            ]);
        }

        return false;
    }

    public function getCompletionStatsForChurch(int $churchId): array
    {
        $firstTimers = FirstTimer::where('church_id', $churchId)->get();
        $totalClasses = FoundationClass::count();

        $completed = 0;
        $inProgress = 0;
        $notStarted = 0;

        foreach ($firstTimers as $ft) {
            $count = $ft->foundationAttendances()->where('completed', true)->count();
            if ($count >= $totalClasses && $totalClasses > 0) {
                $completed++;
            } elseif ($count > 0) {
                $inProgress++;
            } else {
                $notStarted++;
            }
        }

        return compact('completed', 'inProgress', 'notStarted');
    }
}
