<?php

namespace App\Services;

use App\Models\FirstTimer;
use App\Models\Member;
use App\Models\FoundationAttendance;
use App\Models\FoundationClass;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use Illuminate\Support\Facades\Auth;

class FoundationSchoolService
{
    public function getAllClasses()
    {
        return FoundationClass::ordered()->get();
    }

    public function getStudentProgress($person)
    {
        $classes = FoundationClass::ordered()->get();
        $isMember = $person instanceof Member;

        return $classes->map(function ($class) use ($person, $isMember) {
            $query = FoundationAttendance::where('foundation_class_id', $class->id);

            if ($isMember) {
                $query->where('member_id', $person->id);
            } else {
                $query->where('first_timer_id', $person->id);
            }

            $attendance = $query->first();

            return [
                'class' => $class,
                'attendance' => $attendance,
                'attended' => $attendance?->attended ?? false,
                'completed' => $attendance?->completed ?? false,
            ];
        });
    }

    public function recordAttendance($person, int $classId, array $data): FoundationAttendance
    {
        $isMember = $person instanceof Member;

        return FoundationAttendance::updateOrCreate(
            [
                'first_timer_id' => $isMember ? null : $person->id,
                'member_id' => $isMember ? $person->id : null,
                'foundation_class_id' => $classId,
            ],
            [
                'attended' => $data['attended'] ?? true,
                'completed' => $data['completed'] ?? false,
                'attendance_date' => $data['attendance_date'] ?? now(),
            ]
        );
    }

    public function checkAndConvert($person): bool
    {
        $isMember = $person instanceof Member;
        $totalClasses = FoundationClass::count();
        $completedClasses = FoundationAttendance::where($isMember ? 'member_id' : 'first_timer_id', $person->id)
            ->where('completed', true)
            ->count();

        if ($completedClasses >= $totalClasses && $totalClasses > 0) {
            // No longer automatically converting to Retained here.
            // Retained status depends on 6 church attendances now.
            return true;
        }

        // If in progress, we no longer update FirstTimer status here.
        // FirstTimer status (New/Developing/Retained) is now handled strictly 
        // by Church Attendance counts in FirstTimerService.

        return false;
    }

    public function getCompletionStatsForChurch(int $churchId): array
    {
        $ftQuery = FirstTimer::where('church_id', $churchId)->with('foundationAttendances');
        $mQuery = Member::where('church_id', $churchId)->with('foundationAttendances');

        $allPeople = $ftQuery->get()->concat($mQuery->get());
        $totalClasses = FoundationClass::count();

        $completed = 0;
        $inProgress = 0;
        $notStarted = 0;

        foreach ($allPeople as $person) {
            $count = $person->foundationAttendances->where('completed', true)->count();
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

    public function getGroupedProgressData(?string $search = null, ?int $churchId = null)
    {
        $ftQuery = FirstTimer::with(['church', 'foundationAttendances']);
        $mQuery = Member::with(['church', 'foundationAttendances']);

        if ($churchId) {
            $ftQuery->where('church_id', $churchId);
            $mQuery->where('church_id', $churchId);
        }

        if ($search) {
            $ftQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('primary_contact', 'like', "%{$search}%");
            });
            $mQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('primary_contact', 'like', "%{$search}%");
            });
        }

        // Fetch from both tables
        $firstTimers = $ftQuery->latest()->get()->map(function ($item) {
            $item->is_member_record = false;
            return $item;
        });

        $members = $mQuery->latest()->get()->map(function ($item) {
            $item->is_member_record = true;
            return $item;
        });

        $allPeople = $firstTimers->concat($members);

        $statusGroups = [
            'not yet' => collect(),
            'in-progress' => collect(),
            'completed' => collect(),
        ];

        foreach ($allPeople as $person) {
            $status = $person->foundation_school_status;
            if (isset($statusGroups[$status])) {
                $statusGroups[$status]->push($person);
            }
        }

        return $statusGroups;
    }

    public function getHierarchicalProgressData(?string $search = null)
    {
        $categories = ChurchCategory::with(['groups.churches'])->get();

        return $categories->map(function ($category) use ($search) {
            $groups = $category->groups->map(function ($group) use ($search) {
                $churches = $group->churches->map(function ($church) use ($search) {
                    $studentsGrouped = $this->getGroupedProgressData($search, $church->id);
                    $totalStudents = collect($studentsGrouped)->sum(fn($g) => $g->count());

                    return [
                        'id' => $church->id,
                        'name' => $church->name,
                        'students_grouped' => $studentsGrouped,
                        'total_students' => $totalStudents,
                    ];
                })->filter(fn($c) => $c['total_students'] > 0)->values();

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'churches' => $churches,
                    'total_students' => $churches->sum('total_students'),
                ];
            })->filter(fn($g) => $g['churches']->isNotEmpty())->values();

            return [
                'id' => $category->id,
                'name' => $category->name,
                'groups' => $groups,
                'total_students' => $groups->sum('total_students'),
            ];
        })->filter(fn($c) => $c['groups']->isNotEmpty())->values();
    }
}
