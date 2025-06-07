<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use App\Models\UserApp;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function createOnboardingLink(Request $request)

        {
            
            \Log::info('URLS usadas:', [
    'refresh_url' => env('APP_URL') . '/stripe/refresh',
    'return_url'  => env('APP_URL') . '/stripe/return',
]);

            \Log::info('Clave Stripe usada:', ['clave' => config('services.stripe.secret')]);

        Stripe::setApiKey(config('services.stripe.secret'));
        $user = Auth::user();

        // 1. Buscar o crear cuenta Stripe para el usuario
        if (!$user->stripe_account_id) {
            // Creamos una cuenta Express para el profesional
            $account = Account::create([
                'type' => 'express',
                'email' => $user->email,
            ]);
            // Guardamos el ID en la base de datos
            $user->stripe_account_id = $account->id;
            $user->save();
        }

        // 2. Creamos el link de onboarding
        $accountLink = AccountLink::create([
            'account' => $user->stripe_account_id,
            'refresh_url' => env('APP_URL') . '/stripe/refresh', // puedes cambiar la URL
            'return_url' => env('APP_URL') . '/stripe/return',   // puedes cambiar la URL
            'type' => 'account_onboarding',
        ]);

        return response()->json(['url' => $accountLink->url]);
    }
}
