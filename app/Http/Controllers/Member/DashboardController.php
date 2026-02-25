<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Find personal record in either table
        $member = Member::where('user_id', $user->id)->first();
        $firstTimer = null;

        if (!$member) {
            $firstTimer = FirstTimer::where('user_id', $user->id)->first();
        }

        $record = $member ?? $firstTimer;

        if (!$record) {
            // Fallback if no record linked to user
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is not linked to a member profile.');
        }

        $record->load([
            'church.group.category',
            'weeklyAttendances' => function ($q) {
                $q->orderByDesc('service_date');
            },
            'foundationAttendances.foundationClass'
        ]);

        // Bringer Data if applicable
        $isBringer = $user->isBringer();
        $bringer = null;
        $firstTimers = collect();
        $bringerStats = [];

        if ($isBringer) {
            $bringer = \App\Models\Bringer::where('user_id', $user->id)->first();
            if ($bringer) {
                $fts = $bringer->firstTimers()
                    ->with(['church', 'weeklyAttendances', 'foundationAttendances'])
                    ->get();

                $mbs = $bringer->members()
                    ->with(['church', 'weeklyAttendances', 'foundationAttendances'])
                    ->get();

                $allSouls = $fts->concat($mbs)->sortByDesc('date_of_visit');

                $bringerStats = [
                    'total_souls' => $allSouls->count(),
                    'retained' => $allSouls->where('status', 'Retained')->count(),
                    'developing' => $allSouls->where('status', 'Developing')->count(),
                    'new' => $allSouls->where('status', 'New')->count(),
                ];

                // Keep variable names consistent for the view if possible, or update view
                $firstTimers = $allSouls;
            }
        }

        $foundationClasses = \App\Models\FoundationClass::ordered()->get();

        return view('member.dashboard', compact('record', 'member', 'firstTimer', 'isBringer', 'bringer', 'firstTimers', 'bringerStats', 'foundationClasses'));
    }
}
