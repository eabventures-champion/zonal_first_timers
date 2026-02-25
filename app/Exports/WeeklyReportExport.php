<?php

namespace App\Exports;

use App\Services\WeeklyReportService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WeeklyReportExport implements FromView, ShouldAutoSize
{
    private $month;
    private $year;
    private $groupId;
    private $churchId;

    public function __construct($month, $year, $groupId = null, $churchId = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->groupId = $groupId;
        $this->churchId = $churchId;
    }

    public function view(): View
    {
        $reportService = new WeeklyReportService(); // resolve from container if needed, but new is fine here since it has no deps
        $reportData = $reportService->getReportData($this->month, $this->year, $this->groupId, $this->churchId);
        $weeksInMonth = $reportService->getWeeksInMonth($this->month, $this->year);

        $month = $this->month;
        $year = $this->year;

        return view('admin.reports.pdf', compact('reportData', 'month', 'year', 'weeksInMonth'));
    }
}
