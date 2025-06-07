<?php

namespace App\Http\Controllers\Api;

use App\Models\UserApp;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Account as StripeAccount;
use Stripe\AccountLink;
use Stripe\Token as StripeToken;
use Stripe\Exception\InvalidRequestException;
use App\Notifications\ResetPasswordNotification;

class UserAppController extends Controller
{
    public function index()
    {
        return UserApp::with(['languages','locations','addresses'])->get();
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

        $user = UserApp::create([
            'name'             => $validated['name'],
            'last_name'        => $validated['last_name'],
            'email'            => $validated['email'],
            'password'         => $validated['password'],
            'profile_picture'  => $validated['profile_picture'] ?? null,
            'phone_number'     => $validated['phone_number'] ?? null,
            'is_client'        => $validated['is_client'] ?? false,
            'is_professional'  => $validated['is_professional'] ?? false,
            'privacy_policy'   => $validated['privacy_policy'],
            'terms_conditions' => $validated['terms_conditions'],
            'email_verified'   => true,
        ]);

        if ($request->filled('languages')) {
            $user->languages()->sync($request->languages);
        }
        if ($request->filled('locations')) {
            $user->locations()->sync($request->locations);
        }
        if ($request->filled('addresses')) {
            $addressIds = [];
            foreach ($request->addresses as $addrText) {
                $addressIds[] = Address::create(['address' => $addrText])->id;
            }
            $user->addresses()->sync($addressIds);
        }

        // Enviar código de verificación
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_verification_code = $code;
        $user->save();
        Mail::to($user->email)->send(new EmailVerificationMail($user, $code));

        // Stripe Connect (Express)
        $accountLinkUrl = null;
        if ($user->is_professional && config('services.stripe.connect_enabled')) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));

                $acct = StripeAccount::create([
                    'type'    => 'express',
                    'country' => 'ES',
                    'email'   => $user->email,
                ]);

                $user->stripe_account_id = $acct->id;
                $user->save();

                $link = AccountLink::create([
                    'account'     => $acct->id,
                    'refresh_url' => config('app.client_url').'/onboarding/refresh',
                    'return_url'  => config('app.client_url').'/onboarding/success',
                    'type'        => 'account_onboarding',
                ]);

                $accountLinkUrl = $link->url;
            } catch (InvalidRequestException $e) {
                \Log::warning('Stripe Connect error: '.$e->getMessage());
            }
        }

        $response = [
            'user'       => $user->load(['languages','locations','addresses']),
            'message'    => 'Usuario creado. Código de verificación enviado.',
            'debug_code' => $code,
        ];
        if ($accountLinkUrl) {
            $response['stripe_onboarding_url'] = $accountLinkUrl;
        }

        return response()->json($response, 201);
    }

    public function show(UserApp $users_app)
    {
        return $users_app->load(['languages','locations','addresses']);
    }

public function update(Request $request, UserApp $users_app)
{
    // 1) Actualizamos campos básicos (sin password, relaciones ni IBAN)
    $users_app->update($request->except([
        'languages','locations','addresses','password','iban'
    ]));

    // 2) Relacionales
    if ($request->filled('languages')) {
        $users_app->languages()->sync($request->languages);
    }
    if ($request->filled('locations')) {
        $users_app->locations()->sync($request->locations);
    }
    if ($request->filled('addresses')) {
        $ids = [];
        foreach ($request->addresses as $addr) {
            $ids[] = Address::firstOrCreate(['address'=>$addr])->id;
        }
        $users_app->addresses()->sync($ids);
    }

    // 3) Crear o enlazar cuenta Stripe Express si no existe
    if (
        $users_app->is_professional &&
        config('services.stripe.connect_enabled') &&
        ! $users_app->stripe_account_id
    ) {
        Stripe::setApiKey(config('services.stripe.secret'));
        $acct = StripeAccount::create([
            'type'    => 'express',
            'country' => 'ES',
            'email'   => $users_app->email,
        ]);
        $users_app->stripe_account_id = $acct->id;
        $users_app->save();
    }

    // 4) Si han enviado IBAN: creamos token y external account
    if ($request->filled('iban') && $users_app->stripe_account_id) {
        Stripe::setApiKey(config('services.stripe.secret'));

        // 4.1 crea token bancario
        $token = StripeToken::create([
            'bank_account' => [
                'country'             => 'ES',
                'currency'            => 'eur',
                'account_holder_name' => "{$users_app->name} {$users_app->last_name}",
                'account_holder_type' => 'individual',
                'account_number'      => str_replace(' ', '', $request->iban),
            ],
        ]);

        // 4.2 asocia external account
        $external = StripeAccount::createExternalAccount(
            $users_app->stripe_account_id,
            ['external_account' => $token->id]
        );

        // 4.3 guarda en BD
        $users_app->payout_iban       = trim($request->iban);
        $users_app->payout_account_id = $external->id;
        $users_app->save();
    }

    // 5) Devolvemos el user con sus relaciones
    return $users_app->load(['languages','locations','addresses']);
}


    /**
     * Crea y asocia un método de cobro (IBAN) al Connect Account.
     */
    public function updatePayoutMethod(Request $request, $id)
    {
        $request->validate([
            'iban' => 'required|string',
        ]);

        $user = UserApp::findOrFail($id);

        if (! $user->stripe_account_id) {
            return response()->json([
                'message' => 'El profesional no tiene cuenta Stripe Connect.'
            ], 422);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // 1) Crear token bancario
            $stripeToken = StripeToken::create([
                'bank_account' => [
                    'country'             => 'ES',
                    'currency'            => 'eur',
                    'account_holder_name' => "{$user->name} {$user->last_name}",
                    'account_holder_type' => 'individual',
                    'account_number'      => str_replace(' ', '', $request->iban),
                ],
            ]);

            // 2) Asociar external account
            $external = StripeAccount::createExternalAccount(
                $user->stripe_account_id,
                ['external_account' => $stripeToken->id]
            );

            // 3) Guardar en BD
            $user->payout_account_id = $external->id;
            $user->payout_iban       = trim($request->iban);
            $user->save();

            return response()->json([
                'message'           => 'Método de cobro actualizado.',
                'payout_account_id' => $external->id,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error asociando payout method: ' . $e->getMessage());
            return response()->json([
                'message' => 'No se pudo asociar el método de cobro.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(UserApp $users_app)
    {
        $users_app->delete();
        return response()->noContent();
    }

    public function addAddress(Request $request, $id)
    {
        $request->validate(['address' => 'required|string|max:255']);
        $user    = UserApp::findOrFail($id);
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

    // --- Password Reset ---

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users_app,email']);
        $user  = UserApp::where('email', $request->email)->firstOrFail();
        $token = Password::broker('users_app')->createToken($user);
        $user->notify(new ResetPasswordNotification($token));
        return response()->json(['message' => 'Enlace de restablecimiento enviado.'], 200);
    }

    public function resetPassword(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:users_app,email',
            'token'                 => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors'  => $v->errors(),
            ], 422);
        }

        $status = Password::broker('users_app')->reset(
            $request->only('email','password','password_confirmation','token'),
            function($user, $pass) {
                $user->password = Hash::make($pass);
                $user->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => trans($status)], 200);
        }

        return response()->json(['message' => trans($status)], 500);
    }
}
