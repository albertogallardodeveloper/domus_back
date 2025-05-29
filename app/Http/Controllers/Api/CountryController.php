<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    public function index()
    {
        // ✅ Devolver todos los países con sus localidades asociadas
        return Country::with('locations')->get();
    }

    public function store(Request $request)
    {
        return Country::create($request->all());
    }

    public function show(Country $country)
    {
        return $country->load('locations');
    }

    public function update(Request $request, Country $country)
    {
        $country->update($request->all());
        return $country;
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return response()->noContent();
    }
}
