<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return $user->addresses ?? [];
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $address = Address::firstOrCreate(['address' => $request->address]);
        $user->addresses()->syncWithoutDetaching([$address->id]);

        return response()->json($user->addresses, 201);
    }
}