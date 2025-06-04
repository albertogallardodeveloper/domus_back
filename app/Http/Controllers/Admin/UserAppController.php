<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserApp;
use App\Models\Language;
use App\Models\Location;
use App\Models\Address;
use Illuminate\Http\Request;

class UserAppController extends Controller
{
    public function index()
    {
        $users = UserApp::with(['languages', 'locations', 'addresses'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.users-app.index', compact('users'));
    }

    public function create()
    {
        $languages = Language::all();
        $locations = Location::all();
        return view('admin.users-app.create', compact('languages', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users_app,email',
            'password' => 'required|string|min:6|confirmed',
            'profile_picture' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'is_client' => 'boolean',
            'is_professional' => 'boolean',
            'privacy_policy' => 'required|boolean|in:1',
            'terms_conditions' => 'required|boolean|in:1',
            'languages' => 'array',
            'locations' => 'array',
            // addresses se gestionan aparte
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = UserApp::create($validated);

        if ($request->has('languages')) {
            $user->languages()->sync($request->languages);
        }
        if ($request->has('locations')) {
            $user->locations()->sync($request->locations);
        }

        // Las direcciones se gestionan aparte desde editar
        return redirect()->route('admin.users-app.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(UserApp $users_app)
    {
        $languages = Language::all();
        $locations = Location::all();
        $addresses = $users_app->addresses()->pluck('address')->toArray();

        return view('admin.users-app.edit', compact('users_app', 'languages', 'locations', 'addresses'));
    }

    public function update(Request $request, UserApp $users_app)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => "required|email|unique:users_app,email,{$users_app->id}",
            'password' => 'nullable|string|min:6|confirmed',
            'profile_picture' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'is_client' => 'boolean',
            'is_professional' => 'boolean',
            'privacy_policy' => 'required|boolean|in:1',
            'terms_conditions' => 'required|boolean|in:1',
            'languages' => 'array',
            'locations' => 'array',
            // addresses igual, aparte
        ]);

        if ($validated['password']) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $users_app->update($validated);

        $users_app->languages()->sync($request->languages ?? []);
        $users_app->locations()->sync($request->locations ?? []);

        return redirect()->route('admin.users-app.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(UserApp $users_app)
    {
        $users_app->delete();
        return redirect()->route('admin.users-app.index')->with('success', 'Usuario eliminado');
    }
}
