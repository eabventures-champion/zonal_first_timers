<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\Church;
use App\Models\FirstTimer;
use App\Models\Member;
use App\Models\User;

class SidebarComposer
{
    public function compose(View $view)
    {
        $user = auth()->user();
        $isRO = $user && $user->hasRole('Retaining Officer');
        $churchId = $user ? $user->church_id : null;

        $counts = [
            'categories' => ChurchCategory::count(),
            'groups' => ChurchGroup::count(),
            'churches' => Church::count(),
            'first_timers' => FirstTimer::where('status', '!=', 'Retained')
                ->when($isRO && $churchId, function ($q) use ($churchId) {
                    return $q->where('church_id', $churchId);
                })
                ->count(),
            'members' => Member::when($isRO && $churchId, function ($q) use ($churchId) {
                return $q->where('church_id', $churchId);
            })
                ->count(),
            'users' => User::count(),
        ];

        $view->with('sidebarCounts', $counts);
    }
}
