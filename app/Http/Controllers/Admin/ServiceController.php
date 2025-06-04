<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\UserApp;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['category', 'professional'])->orderBy('id', 'desc')->paginate(30);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::orderBy('name')->get();
        $professionals = UserApp::where('is_professional', true)->orderBy('name')->get();
        return view('admin.services.create', compact('categories', 'professionals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_app_id' => 'required|exists:users_app,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric'
        ]);
        Service::create($data);
        return redirect()->route('admin.services.index')->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::orderBy('name')->get();
        $professionals = UserApp::where('is_professional', true)->orderBy('name')->get();
        return view('admin.services.edit', compact('service', 'categories', 'professionals'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'user_app_id' => 'required|exists:users_app,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric'
        ]);
        $service->update($data);
        return redirect()->route('admin.services.index')->with('success', 'Servicio actualizado.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Servicio eliminado.');
    }
}
