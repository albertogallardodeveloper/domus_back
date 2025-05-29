<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;

class ServiceCategoryController extends Controller
{
    /**
     * Devuelve todas las categorías raíz (sin padre), con sus hijas anidadas recursivamente
     */
    public function index()
    {
            return ServiceCategory::where('parent_id', 0)
            ->with('children')
            ->get();
    }

    /**
     * Crea una nueva categoría con opción de categoría padre (parent_id)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:service_categories,id',
        ]);

        $category = ServiceCategory::create($validated);
        return response()->json($category, 201);
    }

    /**
     * Muestra una categoría con sus hijas
     */
    public function show(ServiceCategory $serviceCategory)
    {
        return $serviceCategory->load('children');
    }

    /**
     * Actualiza la categoría (nombre, imagen o parent_id)
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:service_categories,id',
        ]);

        $serviceCategory->update($validated);
        return response()->json($serviceCategory);
    }

    /**
     * Elimina la categoría
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return response()->json(null, 204);
    }
}
