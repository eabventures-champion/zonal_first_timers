<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChurchGroupRequest;
use App\Http\Requests\UpdateChurchGroupRequest;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Services\ChurchHierarchyService;

class ChurchGroupController extends Controller
{
    public function __construct(private ChurchHierarchyService $service)
    {
    }

    public function index()
    {
        $groups = $this->service->getAllGroups();
        $categories = ChurchCategory::all();
        return view('admin.church-groups.index', compact('groups', 'categories'));
    }

    public function create()
    {
        $categories = ChurchCategory::all();
        return view('admin.church-groups.create', compact('categories'));
    }

    public function store(StoreChurchGroupRequest $request)
    {
        $this->service->createGroup($request->validated());
        return redirect()->route('admin.church-groups.index')
            ->with('success', 'Church group created successfully.');
    }

    public function edit(ChurchGroup $churchGroup)
    {
        $categories = ChurchCategory::all();
        return view('admin.church-groups.edit', compact('churchGroup', 'categories'));
    }

    public function update(UpdateChurchGroupRequest $request, ChurchGroup $churchGroup)
    {
        $this->service->updateGroup($churchGroup, $request->validated());
        return redirect()->route('admin.church-groups.index')
            ->with('success', 'Church group updated successfully.');
    }

    public function destroy(ChurchGroup $churchGroup)
    {
        $this->service->deleteGroup($churchGroup);
        return redirect()->route('admin.church-groups.index')
            ->with('success', 'Church group deleted successfully.');
    }
}
