<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChurchCategoryRequest;
use App\Http\Requests\UpdateChurchCategoryRequest;
use App\Models\ChurchCategory;
use App\Services\ChurchHierarchyService;
use Illuminate\Http\Request;

class ChurchCategoryController extends Controller
{
    public function __construct(private ChurchHierarchyService $service)
    {
    }

    public function index()
    {
        $categories = $this->service->getAllCategories();
        return view('admin.church-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.church-categories.create');
    }

    public function store(StoreChurchCategoryRequest $request)
    {
        $this->service->createCategory($request->validated());
        return redirect()->route('admin.church-categories.index')
            ->with('success', 'Church category created successfully.');
    }

    public function edit(ChurchCategory $churchCategory)
    {
        return view('admin.church-categories.edit', compact('churchCategory'));
    }

    public function update(UpdateChurchCategoryRequest $request, ChurchCategory $churchCategory)
    {
        $this->service->updateCategory($churchCategory, $request->validated());
        return redirect()->route('admin.church-categories.index')
            ->with('success', 'Church category updated successfully.');
    }

    public function destroy(ChurchCategory $churchCategory)
    {
        $this->service->deleteCategory($churchCategory);
        return redirect()->route('admin.church-categories.index')
            ->with('success', 'Church category deleted successfully.');
    }
}
