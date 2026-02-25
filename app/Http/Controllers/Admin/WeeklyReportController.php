<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WeeklyReportExport;
use App\Services\WeeklyReportService;
use App\Models\ChurchGroup;
use App\Models\Church;
use Carbon\Carbon;

class WeeklyReportController extends Controller
{
    private $reportService;

    public function __construct(WeeklyReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $groupId = $request->input('group_id');
        $churchId = $request->input('church_id');

        $reportData = $this->reportService->getReportData($month, $year, $groupId, $churchId);
        $weeksInMonth = $this->reportService->getWeeksInMonth($month, $year);

        // For the month/year dropdowns
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = date('F', mktime(0, 0, 0, $m, 1));
        }
        $years = range(now()->year - 2, now()->year);

        // For group and church dropdowns
        $groups = ChurchGroup::orderBy('name')->get();
        $churches = Church::orderBy('name')->get();

        return view('admin.reports.weekly', compact(
            'reportData',
            'month',
            'year',
            'weeksInMonth',
            'months',
            'years',
            'groups',
            'churches',
            'groupId',
            'churchId'
        ));
    }

    public function exportExcel(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $groupId = $request->input('group_id');
        $churchId = $request->input('church_id');

        return Excel::download(
            new WeeklyReportExport($month, $year, $groupId, $churchId),
            "Weekly_Report_{$year}_{$month}.xlsx"
        );
    }

    public function exportPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $groupId = $request->input('group_id');
        $churchId = $request->input('church_id');

        $reportData = $this->reportService->getReportData($month, $year, $groupId, $churchId);
        $weeksInMonth = $this->reportService->getWeeksInMonth($month, $year);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('reportData', 'month', 'year', 'weeksInMonth'))
            ->setPaper('a4', 'landscape');
        return $pdf->download("Weekly_Report_{$year}_{$month}.pdf");
    }
}
