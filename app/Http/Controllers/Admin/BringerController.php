<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bringer;
use App\Models\Church;
use App\Models\ChurchCategory;
use Illuminate\Http\Request;

class BringerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('Retaining Officer')) {
            $churchId = $user->church_id;
            if (!$churchId) {
                return redirect()->route('ro.dashboard')->with('error', 'You are not assigned to a church.');
            }

            $church = Church::with(['bringers.firstTimers', 'bringers.members'])->findOrFail($churchId);
            return view('ro.bringers.index', compact('church'));
        }

        $categories = ChurchCategory::with(['groups.churches.bringers.firstTimers', 'groups.churches.bringers.members'])->get();

        // Sort groups and churches by total souls count (first timers + members) descending
        $categories->each(function ($category) {
            $sortedGroups = $category->groups->sortByDesc(function ($group) {
                return $group->churches->sum(fn($church) => $church->bringers->sum(fn($b) => $b->firstTimers->count() + $b->members->count()));
            })->values();

            $category->setRelation('groups', $sortedGroups);

            $category->groups->each(function ($group) {
                $group->setRelation('churches', $group->churches->sortByDesc(function ($church) {
                    return $church->bringers->sum(fn($b) => $b->firstTimers->count() + $b->members->count());
                })->values());
            });
        });

        return view('admin.bringers.index', compact('categories'));
    }

    public function getForChurch(Church $church)
    {
        $bringers = Bringer::where('church_id', $church->id)->get();
        return response()->json($bringers);
    }

    public function checkContact(Request $request)
    {
        $exists = Bringer::where('contact', $request->contact)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'A Bringer with this contact already exists. Please select them from the list above.' : ''
        ]);
    }
}
