<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::withCount('locations')->orderBy('name')->paginate(15);
        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'iso_code' => 'required|string|max:5|unique:countries',
            'phone_code' => 'nullable|string|max:10',
        ]);

        Country::create($data);
        return redirect()->route('admin.countries.index')->with('success', 'País creado correctamente.');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'iso_code' => 'required|string|max:5|unique:countries,iso_code,' . $country->id,
            'phone_code' => 'nullable|string|max:10',
        ]);

        $country->update($data);
        return redirect()->route('admin.countries.index')->with('success', 'País actualizado.');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('admin.countries.index')->with('success', 'País eliminado.');
    }
}
