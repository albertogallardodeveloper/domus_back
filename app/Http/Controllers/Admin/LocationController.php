<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Country;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('country')->orderBy('name')->paginate(20);
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.locations.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'country_id' => 'required|exists:countries,id',
        ]);

        Location::create($data);
        return redirect()->route('admin.locations.index')->with('success', 'Localización creada correctamente.');
    }

    public function edit(Location $location)
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.locations.edit', compact('location', 'countries'));
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'country_id' => 'required|exists:countries,id',
        ]);

        $location->update($data);
        return redirect()->route('admin.locations.index')->with('success', 'Localización actualizada.');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('admin.locations.index')->with('success', 'Localización eliminada.');
    }
}
