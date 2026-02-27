<?php

namespace App\Services;

use App\Models\Church;
use App\Models\FirstTimer;
use Carbon\Carbon;

class WeeklyReportService
{
    public function getWeeksInMonth($month, $year)
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $weeks = [];
        $current = $startOfMonth->copy();

        // If the month doesn't start on a Sunday, the first "week" starts on the 1st
        // and ends on the first Saturday of the month.
        $weekNum = 1;
        while ($current <= $endOfMonth) {
            $endOfWeek = $current->copy()->endOfWeek(Carbon::SATURDAY)->endOfDay();
            if ($endOfWeek > $endOfMonth) {
                $endOfWeek = $endOfMonth->copy();
            }

            $weeks[$weekNum] = [
                'start' => $current->format('M d'),
                'end' => $endOfWeek->format('M d'),
                'start_date' => $current->copy(),
                'end_date' => $endOfWeek->copy()
            ];

            $current = $endOfWeek->copy()->addSecond()->startOfDay();
            if ($current->dayOfWeek !== Carbon::SUNDAY && $current <= $endOfMonth) {
                // Should not happen with endOfWeek(Carbon::SATURDAY)
                $current->startOfWeek(Carbon::SUNDAY);
            }
            $weekNum++;
        }
        return $weeks;
    }

    public function getReportData($month, $year, $groupId = null, $churchId = null)
    {
        $weeksInMonth = $this->getWeeksInMonth($month, $year);
        $churchQuery = Church::with(['group.category']);

        if ($churchId) {
            $churchQuery->where('id', $churchId);
        } elseif ($groupId) {
            $churchQuery->where('church_group_id', $groupId);
        }

        $churches = $churchQuery->get();

        $ftQuery = FirstTimer::whereYear('date_of_visit', $year)
            ->whereMonth('date_of_visit', $month);

        $memberQuery = \App\Models\Member::whereYear('date_of_visit', $year)
            ->whereMonth('date_of_visit', $month);

        if ($churchId) {
            $ftQuery->where('church_id', $churchId);
            $memberQuery->where('church_id', $churchId);
        } elseif ($groupId) {
            $ftQuery->whereHas('church', function ($q) use ($groupId) {
                $q->where('church_group_id', $groupId);
            });
            $memberQuery->whereHas('church', function ($q) use ($groupId) {
                $q->where('church_group_id', $groupId);
            });
        }

        $firstTimers = $ftQuery->get();
        $membersAsFirstTimers = $memberQuery->get();
        $allFirstTimers = $firstTimers->concat($membersAsFirstTimers);

        $data = [];

        // Initialize structure
        foreach ($churches as $church) {
            $group = $church->group;
            $category = $group ? $group->category : null;

            $catName = $category ? $category->name : 'Uncategorized';
            $groupName = $group ? $group->name : 'Ungrouped';
            $churchName = $church->name;

            if (!isset($data[$catName])) {
                $data[$catName] = [
                    'groups' => [],
                    'weeks' => array_fill_keys(array_keys($weeksInMonth), 0),
                    'total' => 0
                ];
            }
            if (!isset($data[$catName]['groups'][$groupName])) {
                $data[$catName]['groups'][$groupName] = [
                    'churches' => [],
                    'weeks' => array_fill_keys(array_keys($weeksInMonth), 0),
                    'total' => 0
                ];
            }
            if (!isset($data[$catName]['groups'][$groupName]['churches'][$churchName])) {
                $data[$catName]['groups'][$groupName]['churches'][$churchName] = [
                    'weeks' => array_fill_keys(array_keys($weeksInMonth), 0),
                    'total' => 0
                ];
            }
        }

        foreach ($allFirstTimers as $ft) {
            $church = $churches->firstWhere('id', $ft->church_id);
            if (!$church)
                continue;

            $group = $church->group;
            $category = $group ? $group->category : null;

            $catName = $category ? $category->name : 'Uncategorized';
            $groupName = $group ? $group->name : 'Ungrouped';
            $churchName = $church->name;

            $dateOfVisit = Carbon::parse($ft->date_of_visit)->startOfDay();

            $assignedWeek = null;
            foreach ($weeksInMonth as $weekNum => $weekDates) {
                if ($dateOfVisit->between($weekDates['start_date'], $weekDates['end_date'])) {
                    $assignedWeek = $weekNum;
                    break;
                }
            }

            if ($assignedWeek === null) {
                continue;
            }

            // Church data
            $data[$catName]['groups'][$groupName]['churches'][$churchName]['weeks'][$assignedWeek]++;
            $data[$catName]['groups'][$groupName]['churches'][$churchName]['total']++;

            // Group totals
            $data[$catName]['groups'][$groupName]['weeks'][$assignedWeek]++;
            $data[$catName]['groups'][$groupName]['total']++;

            // Category totals
            $data[$catName]['weeks'][$assignedWeek]++;
            $data[$catName]['total']++;
        }

        ksort($data); // sort categories
        foreach ($data as $catName => &$catData) {
            ksort($catData['groups']); // sort groups
            foreach ($catData['groups'] as $groupName => &$groupData) {
                ksort($groupData['churches']); // sort churches
            }
        }

        return $data;
    }
}
