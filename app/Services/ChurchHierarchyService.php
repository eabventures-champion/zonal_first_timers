<?php

namespace App\Services;

use App\Models\Church;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use Illuminate\Support\Facades\Auth;

class ChurchHierarchyService
{
    // ── Church Categories ──────────────────────────────────

    public function getAllCategories()
    {
        return ChurchCategory::with(['groups'])->withCount('groups')->latest()->get();
    }

    public function createCategory(array $data): ChurchCategory
    {
        $data['created_by'] = Auth::id();
        return ChurchCategory::create($data);
    }

    public function updateCategory(ChurchCategory $category, array $data): ChurchCategory
    {
        $data['updated_by'] = Auth::id();
        $category->update($data);
        return $category->fresh();
    }

    public function deleteCategory(ChurchCategory $category): void
    {
        $category->delete();
    }

    // ── Church Groups ──────────────────────────────────────

    public function getAllGroups()
    {
        return ChurchGroup::with(['category', 'churches.retainingOfficer'])->withCount('churches')->latest()->get();
    }

    public function getGroupsByCategory($categoryId)
    {
        return ChurchGroup::where('church_category_id', $categoryId)
            ->withCount('churches')
            ->get();
    }

    public function createGroup(array $data): ChurchGroup
    {
        $data['created_by'] = Auth::id();
        return ChurchGroup::create($data);
    }

    public function updateGroup(ChurchGroup $group, array $data): ChurchGroup
    {
        $data['updated_by'] = Auth::id();
        $group->update($data);
        return $group->fresh();
    }

    public function getAllGroupsWithChurches()
    {
        return ChurchGroup::with([
            'churches' => function ($q) {
                $q->with(['retainingOfficer'])->withStats();
            },
            'category'
        ])
            ->join('church_categories', 'church_groups.church_category_id', '=', 'church_categories.id')
            ->orderByRaw("CASE WHEN church_categories.name = 'MAIN CHURCH' THEN 0 ELSE 1 END")
            ->orderBy('church_categories.name')
            ->select('church_groups.*')
            ->get();
    }

    public function deleteGroup(ChurchGroup $group): void
    {
        $group->delete();
    }

    // ── Churches ───────────────────────────────────────────

    public function getAllChurches()
    {
        return Church::with(['group.category', 'retainingOfficer'])
            ->withStats()
            ->latest()
            ->get();
    }

    public function getChurchesByGroup($groupId)
    {
        return Church::where('church_group_id', $groupId)
            ->with(['retainingOfficer'])
            ->withStats()
            ->get();
    }

    public function createChurch(array $data): Church
    {
        $data['created_by'] = Auth::id();
        return Church::create($data);
    }

    public function updateChurch(Church $church, array $data): Church
    {
        $data['updated_by'] = Auth::id();
        $church->update($data);
        return $church->fresh();
    }

    public function getAllCategoriesWithHierarchy()
    {
        return ChurchCategory::with(['groups.churches'])->get();
    }

    public function deleteChurch(Church $church): void
    {
        $church->delete();
    }
}
