<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirstTimer;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TrashController extends Controller
{
    /**
     * Get the accurate total trash count (used by SidebarComposer).
     * Applies the same deduplication logic as index().
     */
    public static function getTotalTrashCount(): int
    {
        $trashedMembers = Member::onlyTrashed()->get();
        $trashedFTs = FirstTimer::onlyTrashed()->get();
        $memberUserIds = $trashedMembers->pluck('user_id')->filter()->toArray();
        $ftUserIds = $trashedFTs->pluck('user_id')->filter()->toArray();
        $coveredUserIds = array_unique(array_merge($memberUserIds, $ftUserIds));

        // Filter FTs same as index() — hide FTs that have a corresponding Member
        $visibleFTs = $trashedFTs->filter(function ($ft) use ($memberUserIds) {
            if ($ft->user_id && in_array($ft->user_id, $memberUserIds))
                return false;
            if ($ft->user_id && Member::where('user_id', $ft->user_id)->exists())
                return false;
            return true;
        });

        $trashedUserCount = User::onlyTrashed()->whereNotIn('id', $coveredUserIds)->count();

        return $trashedMembers->count() + $visibleFTs->count() + $trashedUserCount;
    }

    public function index(Request $request)
    {
        // Get all trashed members
        $members = Member::onlyTrashed()
            ->with(['church', 'user'])
            ->latest('deleted_at')
            ->get()
            ->map(function ($m) {
                $m->trash_type = 'member';
                $m->display_status = 'Retained Member';
                return $m;
            });

        // Get all trashed first timers
        $firstTimers = FirstTimer::onlyTrashed()
            ->with(['church', 'user'])
            ->latest('deleted_at')
            ->get()
            ->map(function ($ft) {
                $ft->trash_type = 'first_timer';
                $ft->display_status = 'First Timer';
                return $ft;
            });

        // Get all trashed users (that don't already appear via Member/FT)
        $memberUserIds = $members->pluck('user_id')->filter()->toArray();
        $ftUserIds = $firstTimers->pluck('user_id')->filter()->toArray();
        $coveredUserIds = array_unique(array_merge($memberUserIds, $ftUserIds));

        $trashedUsers = User::onlyTrashed()
            ->with('church')
            ->whereNotIn('id', $coveredUserIds)
            ->latest('deleted_at')
            ->get()
            ->map(function ($u) {
                $u->trash_type = 'user';
                $u->display_status = 'User Account';
                $u->full_name = $u->name;
                $u->primary_contact = $u->phone ?? $u->email;
                return $u;
            });

        // Merge and Filter: If a user has both a trashed FT and a trashed Member,
        // we likely only want to see the Member one (since it's the more recent state).
        // Or if the FT record was trashed during promotion, it's just a shell.
        $unifiedItems = $members->concat(
            $firstTimers->filter(function ($ft) use ($memberUserIds) {
                // Hide FT record if there is a Member record (active or trashed) for this user
                if ($ft->user_id && in_array($ft->user_id, $memberUserIds)) {
                    return false;
                }
                // Also check if they are currently an active member
                if ($ft->user_id && Member::where('user_id', $ft->user_id)->exists()) {
                    return false;
                }
                return true;
            })
        )->concat($trashedUsers)->sortByDesc('deleted_at');

        // Manual Pagination for the merged collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentItems = $unifiedItems->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $items = new LengthAwarePaginator(
            $currentItems,
            $unifiedItems->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('admin.trash.index', compact('items'));
    }

    public function restore($type, $id)
    {
        if ($type === 'user') {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();
            return back()->with('success', 'User account restored successfully.');
        }

        $model = $type === 'member' ? Member::onlyTrashed() : FirstTimer::onlyTrashed();
        $item = $model->findOrFail($id);

        // Restore the soul profile (this also restores attendances via model events)
        $item->restore();

        // If there's an associated user that was also deleted, restore it too
        if ($item->user_id) {
            $user = User::onlyTrashed()->find($item->user_id);
            if ($user) {
                $user->restore();
            }
        }

        return back()->with('success', 'Record restored successfully with all history.');
    }

    public function forceDelete($type, $id)
    {
        if ($type === 'user') {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->forceDelete();
            return back()->with('success', 'User account permanently deleted.');
        }

        $model = $type === 'member' ? Member::onlyTrashed() : FirstTimer::onlyTrashed();
        $item = $model->findOrFail($id);

        // If there's an associated user, we might want to clean it up too
        if ($item->user_id) {
            $user = User::onlyTrashed()->find($item->user_id);
            if ($user) {
                $user->forceDelete();
            }
        }

        $item->forceDelete();

        return back()->with('success', 'Record and all associated data permanently deleted.');
    }

    public function bulkRestore(Request $request)
    {
        $selected = $request->input('ids', []);
        if (empty($selected)) {
            return back()->with('error', 'No items selected for restoration.');
        }

        $count = 0;
        foreach ($selected as $compositeId) {
            [$type, $id] = explode(':', $compositeId);

            if ($type === 'user') {
                $user = User::onlyTrashed()->find($id);
                if ($user) {
                    $user->restore();
                    $count++;
                }
                continue;
            }

            $model = $type === 'member' ? Member::onlyTrashed() : FirstTimer::onlyTrashed();
            $item = $model->find($id);

            if ($item) {
                $item->restore();
                if ($item->user_id) {
                    $user = User::onlyTrashed()->find($item->user_id);
                    if ($user) {
                        $user->restore();
                    }
                }
                $count++;
            }
        }

        return back()->with('success', "Successfully restored {$count} records.");
    }

    public function bulkForceDelete(Request $request)
    {
        $selected = $request->input('ids', []);
        if (empty($selected)) {
            return back()->with('error', 'No items selected for purging.');
        }

        $count = 0;
        foreach ($selected as $compositeId) {
            [$type, $id] = explode(':', $compositeId);

            if ($type === 'user') {
                $user = User::onlyTrashed()->find($id);
                if ($user) {
                    $user->forceDelete();
                    $count++;
                }
                continue;
            }

            $model = $type === 'member' ? Member::onlyTrashed() : FirstTimer::onlyTrashed();
            $item = $model->find($id);

            if ($item) {
                if ($item->user_id) {
                    $user = User::onlyTrashed()->find($item->user_id);
                    if ($user) {
                        $user->forceDelete();
                    }
                }
                $item->forceDelete();
                $count++;
            }
        }

        return back()->with('success', "Successfully purged {$count} records permanently.");
    }
}
