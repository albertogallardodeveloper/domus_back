<?php

namespace App\Http\Controllers\Api;

use App\Models\UserApp;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Notifications\EmailVerificationCode;

class UserAppController extends Controller
{
    public function index()
    {
        return UserApp::with(['languages', 'locations', 'addresses'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users_app,email',
            'password' => 'required|string|min:6',
            'profile_picture' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'is_client' => 'boolean',
            'is_professional' => 'boolean',
            'privacy_policy' => 'required|boolean|in:1',
            'terms_conditions' => 'required|boolean|in:1',
            'languages' => 'array',
            'locations' => 'array',
            'addresses' => 'array',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = UserApp::create($validated);

        if ($request->has('languages')) {
            $user->languages()->sync($request->languages);
        }

        if ($request->has('locations')) {
            $user->locations()->sync($request->locations);
        }

        if ($request->has('addresses')) {
            $addressIds = [];

            foreach ($request->addresses as $addrText) {
                $address = Address::create(['address' => $addrText]);
                $addressIds[] = $address->id;
            }

            $user->addresses()->sync($addressIds);
        }

        // ✅ Generar y guardar código de verificación
        $code = rand(100000, 999999);
        Cache::put('verify_' . $user->email, $code, now()->addMinutes(10));

        // ✅ Enviar código al correo
        $user->notify(new EmailVerificationCode($code));

        return response()->json([
            'user' => $user->load(['languages', 'locations', 'addresses']),
            'message' => 'Usuario creado. Código de verificación enviado.',
            'debug_code' => $code,
        ], 201);
    }

    public function show(UserApp $users_app)
    {
        return $users_app->load(['languages', 'locations', 'addresses']);
    }

    public function update(Request $request, UserApp $users_app)
    {
        $users_app->update($request->except(['languages', 'locations', 'addresses', 'password']));

        if ($request->has('languages')) {
            $users_app->languages()->sync($request->languages);
        }

        if ($request->has('locations')) {
            $users_app->locations()->sync($request->locations);
        }

        if ($request->has('addresses')) {
            $addressIds = [];

            foreach ($request->addresses as $addrText) {
                $address = Address::create(['address' => $addrText]);
                $addressIds[] = $address->id;
            }

            $users_app->addresses()->sync($addressIds);
        }

        return $users_app->load(['languages', 'locations', 'addresses']);
    }

    public function destroy(UserApp $users_app)
    {
        $users_app->delete();
        return response()->noContent();
    }

    public function addAddress(Request $request, $id)
    {
        $request->validate(['address' => 'required|string|max:255']);
        $user = UserApp::findOrFail($id);

        $address = Address::firstOrCreate(['address' => $request->address]);
        $user->addresses()->attach($address->id);

        return response()->json($address);
    }

}
