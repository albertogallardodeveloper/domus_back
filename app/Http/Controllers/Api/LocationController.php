<?php

namespace App\Http\Controllers\Api;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function index()
    {
        return Location::with('country')->get();
    }

    public function store(Request $request)
    {
        return Location::create($request->all());
    }

    public function show(Location $location)
    {
        return $location->load('country');
    }

    public function update(Request $request, Location $location)
    {
        $location->update($request->all());
        return $location->load('country');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return response()->noContent();
    }
}

