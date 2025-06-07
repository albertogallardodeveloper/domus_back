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
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Exception\InvalidRequestException;
use App\Notifications\ResetPasswordNotification;

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

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_verification_code = $code;
        $user->save();
        Mail::to($user->email)->send(new EmailVerificationMail($user, $code));

        $accountLinkUrl = null;
        if ($user->is_professional && config('services.stripe.connect_enabled')) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));

                $account = Account::create([
                    'type'          => 'custom',
                    'country'       => 'ES',
                    'email'         => $user->email,
                    'business_type' => 'individual',
                    'capabilities'  => ['transfers' => ['requested' => true]],
                ]);

                $user->stripe_account_id = $account->id;
                $user->save();

                $accountLink = AccountLink::create([
                    'account'     => $account->id,
                    'refresh_url' => config('app.client_url').'/onboarding/refresh',
                    'return_url'  => config('app.client_url').'/onboarding/success',
                    'type'        => 'account_onboarding',
                ]);

                $accountLinkUrl = $accountLink->url;
            } catch (InvalidRequestException $e) {
                \Log::warning('Stripe Connect error: '.$e->getMessage());
            }
        }

        $response = [
            'user'        => $user->load(['languages','locations','addresses']),
            'message'     => 'Usuario creado. Código de verificación enviado.',
            'debug_code'  => $code,
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
        $users_app->update($request->except(['languages','locations','addresses','password']));

        if ($request->has('languages')) {
            $users_app->languages()->sync($request->languages);
        }
        if ($request->has('locations')) {
            $users_app->locations()->sync($request->locations);
        }
        if ($request->has('addresses')) {
            $addressIds = [];
            foreach ($request->addresses as $addrText) {
                $address = Address::firstOrCreate(['address' => $addrText]);
                $addressIds[] = $address->id;
            }
            $users_app->addresses()->sync($addressIds);
        }

        if (
            $request->has('is_professional') &&
            $users_app->is_professional &&
            !$users_app->stripe_account_id &&
            config('services.stripe.connect_enabled')
        ) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $account = Account::create([
                    'type'          => 'custom',
                    'country'       => 'ES',
                    'email'         => $users_app->email,
                    'business_type' => 'individual',
                    'capabilities'  => ['transfers' => ['requested' => true]],
                ]);
                $users_app->stripe_account_id = $account->id;
                $users_app->save();
            } catch (InvalidRequestException $e) {
                \Log::warning('Stripe on-demand error: '.$e->getMessage());
            }
        }

        return $users_app->load(['languages','locations','addresses']);
    }

    public function destroy(UserApp $users_app)
    {
        $users_app->delete();
        return response()->noContent();
    }

    public function addAddress(Request $request, $id)
    {
        $request->validate(['address'=>'required|string|max:255']);
        $user = UserApp::findOrFail($id);
        $address = Address::firstOrCreate(['address'=>$request->address]);
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

    /**
     * Send reset link to user via custom notification.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email'=>'required|email|exists:users_app,email',
        ]);

        $user = UserApp::where('email',$request->email)->firstOrFail();

        // Generate token
        $token = Password::broker('users_app')->createToken($user);

        // Notify via our custom Notification
        $user->notify(new ResetPasswordNotification($token));

        return response()->json([
            'message'=>'Enlace de restablecimiento enviado correctamente.'
        ], 200);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:users_app,email',
            'token'                 => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'Validación fallida',
                'errors'=> $validator->errors(),
            ], 422);
        }

        $status = Password::broker('users_app')->reset(
            $request->only('email','password','password_confirmation','token'),
            function($user,$password){
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message'=>trans($status)],200);
        }

        return response()->json(['message'=>trans($status)],500);
    }
}
