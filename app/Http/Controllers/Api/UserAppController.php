<?php

namespace App\Http\Controllers\Api;

use App\Models\UserApp;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;

class UserAppController extends Controller
{
    public function index()
    {
        return UserApp::with(['languages', 'locations', 'addresses'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email|unique:users_app,email',
            'password'         => 'required|string|min:6',
            'profile_picture'  => 'nullable|string',
            'phone_number'     => 'nullable|string',
            'is_client'        => 'boolean',
            'is_professional'  => 'boolean',
            'privacy_policy'   => 'required|boolean|in:1',
            'terms_conditions' => 'required|boolean|in:1',
            'languages'        => 'array',
            'locations'        => 'array',
            'addresses'        => 'array',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        // 1) Crear el usuario (email_verified por defecto en false)
        $user = UserApp::create([
            'name'                   => $validated['name'],
            'last_name'              => $validated['last_name'],
            'email'                  => $validated['email'],
            'password'               => $validated['password'],
            'profile_picture'        => $validated['profile_picture'] ?? null,
            'phone_number'           => $validated['phone_number'] ?? null,
            'is_client'              => $validated['is_client'] ?? false,
            'is_professional'        => $validated['is_professional'] ?? false,
            'privacy_policy'         => $validated['privacy_policy'],
            'terms_conditions'       => $validated['terms_conditions'],
            'email_verified'         => false,
        ]);

        // 2) Sincronizar relaciones
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

        // 3) Generar código de verificación y enviar por correo
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_verification_code = $code;
        $user->save();

        Mail::to($user->email)->send(new EmailVerificationMail($user, $code));

        // 4) Si es profesional, crear cuenta Connect en Stripe
        $accountLinkUrl = null;
        if ($user->is_professional) {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $account = Account::create([
                'type'          => 'custom',
                'country'       => 'ES',
                'email'         => $user->email,
                'business_type' => 'individual',
                'capabilities'  => [
                    'transfers' => ['requested' => true],
                ],
            ]);

            $user->stripe_account_id = $account->id;
            $user->save();

            $accountLink = AccountLink::create([
                'account'     => $account->id,
                'refresh_url' => config('app.client_url') . '/onboarding/refresh',
                'return_url'  => config('app.client_url') . '/onboarding/success',
                'type'        => 'account_onboarding',
            ]);

            $accountLinkUrl = $accountLink->url;
        }

        // 5) Devolver respuesta JSON (sin exponer el código en producción)
        $response = [
            'user'    => $user->load(['languages', 'locations', 'addresses']),
            'message' => 'Usuario creado. Código de verificación enviado.',
            'debug_code' => $code, // eliminar esta línea en producción
        ];

        if ($accountLinkUrl) {
            $response['stripe_onboarding_url'] = $accountLinkUrl;
        }

        return response()->json($response, 201);
    }

    public function show(UserApp $users_app)
    {
        return $users_app->load(['languages', 'locations', 'addresses']);
    }

    public function update(Request $request, UserApp $users_app)
    {
        // 1) Actualiza campos simples (sin password ni relaciones)
        $users_app->update($request->except(['languages', 'locations', 'addresses', 'password']));

        // 2) Sincronizar idiomas
        if ($request->has('languages')) {
            $users_app->languages()->sync($request->languages);
        }

        // 3) Sincronizar ubicaciones
        if ($request->has('locations')) {
            $users_app->locations()->sync($request->locations);
        }

        // 4) Direcciones: crea nuevas si no existen y sincroniza
        if ($request->has('addresses')) {
            $addressIds = [];
            foreach ($request->addresses as $addrText) {
                $address = Address::firstOrCreate(['address' => $addrText]);
                $addressIds[] = $address->id;
            }
            $users_app->addresses()->sync($addressIds);
        }

        // 5) Si cambió a profesional y no tiene stripe_account_id, crear cuenta Connect
        if ($request->has('is_professional') && $users_app->is_professional && !$users_app->stripe_account_id) {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $account = Account::create([
                'type'          => 'custom',
                'country'       => 'ES',
                'email'         => $users_app->email,
                'business_type' => 'individual',
                'capabilities'  => [
                    'transfers' => ['requested' => true],
                ],
            ]);

            $users_app->stripe_account_id = $account->id;
            $users_app->save();
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

    public function languages()
    {
        return \App\Models\Language::all();
    }

    public function locations()
    {
        return \App\Models\Location::all();
    }
}
