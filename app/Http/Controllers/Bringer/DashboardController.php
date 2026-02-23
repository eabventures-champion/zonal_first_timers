<?php

namespace App\Http\Controllers\Bringer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bringer = \App\Models\Bringer::where('user_id', $user->id)->firstOrFail();

        $firstTimers = $bringer->firstTimers()
            ->with(['church', 'weeklyAttendances', 'foundationAttendances'])
            ->orderByDesc('date_of_visit')
            ->get();

        $stats = [
            'total_souls' => $firstTimers->count(),
            'retained' => $firstTimers->where('status', 'Retained')->count(),
            'developing' => $firstTimers->where('status', 'Developing')->count(),
            'new' => $firstTimers->where('status', 'New')->count(),
        ];

        $foundationClasses = \App\Models\FoundationClass::ordered()->get();

        return view('bringer.dashboard', compact('bringer', 'firstTimers', 'stats', 'foundationClasses'));
    }
}
