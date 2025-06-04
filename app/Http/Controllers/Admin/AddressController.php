<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\UserApp;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::withCount('users')->orderBy('address')->paginate(30);
        return view('admin.addresses.index', compact('addresses'));
    }

    public function create()
    {
        $users = UserApp::orderBy('name')->get();
        return view('admin.addresses.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string|max:255|unique:addresses,address',
            'users' => 'nullable|array',
            'users.*' => 'exists:users_app,id'
        ]);
        $address = Address::create(['address' => $data['address']]);
        if (!empty($data['users'])) {
            $address->users()->sync($data['users']);
        }
        return redirect()->route('admin.addresses.index')->with('success', 'Dirección creada correctamente.');
    }

    public function edit(Address $address)
    {
        $users = UserApp::orderBy('name')->get();
        $selectedUsers = $address->users->pluck('id')->toArray();
        return view('admin.addresses.edit', compact('address', 'users', 'selectedUsers'));
    }

    public function update(Request $request, Address $address)
    {
        $data = $request->validate([
            'address' => 'required|string|max:255|unique:addresses,address,' . $address->id,
            'users' => 'nullable|array',
            'users.*' => 'exists:users_app,id'
        ]);
        $address->update(['address' => $data['address']]);
        $address->users()->sync($data['users'] ?? []);
        return redirect()->route('admin.addresses.index')->with('success', 'Dirección actualizada.');
    }

    public function destroy(Address $address)
    {
        $address->users()->detach();
        $address->delete();
        return redirect()->route('admin.addresses.index')->with('success', 'Dirección eliminada.');
    }
}
