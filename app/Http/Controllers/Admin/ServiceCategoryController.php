<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::with('parent')->orderBy('name')->paginate(30);
        return view('admin.service-categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = ServiceCategory::whereNull('parent_id')->orWhere('parent_id', 0)->orderBy('name')->get();
        return view('admin.service-categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'parent_id' => 'nullable|exists:service_categories,id',
        ]);
        if (isset($data['image'])) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        ServiceCategory::create($data);
        return redirect()->route('admin.service-categories.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        $parents = ServiceCategory::whereNull('parent_id')
            ->orWhere('parent_id', 0)
            ->where('id', '!=', $serviceCategory->id)
            ->orderBy('name')->get();
        return view('admin.service-categories.edit', compact('serviceCategory', 'parents'));
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'parent_id' => 'nullable|exists:service_categories,id',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        $serviceCategory->update($data);
        return redirect()->route('admin.service-categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return redirect()->route('admin.service-categories.index')->with('success', 'Categoría eliminada.');
    }
}
