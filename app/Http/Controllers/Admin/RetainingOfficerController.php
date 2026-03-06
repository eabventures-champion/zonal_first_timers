<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RetainingOfficerController extends Controller
{
    public function index()
    {
        // Fetch users with the 'Retaining Officer' role, grouped by their church's category
        $officers = User::role('Retaining Officer')
            ->with(['church.group.category'])
            ->get()
            ->groupBy(function ($user) {
                return $user->church && $user->church->group && $user->church->group->category
                    ? $user->church->group->category->name
                    : 'Uncategorized';
            });

        // Sort categories to put 'ZONAL CHURCH' first if it exists
        $officers = $officers->sortKeysUsing(function ($a, $b) {
            if ($a === 'ZONAL CHURCH')
                return -1;
            if ($b === 'ZONAL CHURCH')
                return 1;
            return strcasecmp($a, $b);
        });

        return view('admin.retaining-officers.index', compact('officers'));
    }
}
