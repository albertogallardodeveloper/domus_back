<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::with('category', 'professional')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_app_id' => 'required|exists:users_app,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric'
        ]);

        $service = Service::create($validated);

        return response()->json($service->load('category', 'professional'), 201);
    }

    public function show(Service $service)
    {
        return $service->load('category', 'professional');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'service_category_id' => 'sometimes|required|exists:service_categories,id'
        ]);

        $service->update($validated);

        return response()->json($service->load('category', 'professional'));
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json(null, 204);
    }

    public function byCategory($categoryId)
    {
        return Service::with('professional')
            ->where('service_category_id', $categoryId)
            ->get();
    }

}
