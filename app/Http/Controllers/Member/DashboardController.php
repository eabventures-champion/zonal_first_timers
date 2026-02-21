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

        return view('member.dashboard', compact('record', 'member', 'firstTimer'));
    }
}
