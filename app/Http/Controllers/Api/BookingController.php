<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\UserApp;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_app_id' => 'required|exists:users_app,id',
            'service_id' => 'required|exists:services,id',
            'price' => 'required|numeric|min:1',
            'duration' => 'required|numeric|min:1',
            'address' => 'required|string',
            'service_day' => 'required|date',
            'additional_details' => 'nullable|string',
            'promo_code' => 'nullable|string',
        ]);

        $service = \App\Models\Service::findOrFail($request->service_id);
        $pricePerHour = $service->price;
        $hours = $request->duration / 60;
        $subtotal = $pricePerHour * $hours;

        $promoCode = null;
        $discountPercent = 0;

        if ($request->filled('promo_code')) {
            $promoCode = \App\Models\PromoCode::where('code', strtoupper($request->promo_code))
                ->where('active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->where(function ($q) {
                    $q->whereNull('max_redemptions')->orWhereColumn('redemptions', '<', 'max_redemptions');
                })
                ->first();

            if ($promoCode) {
                $discountPercent = $promoCode->discount_percent;
            }
        }

        $discountedTotal = round($subtotal * (1 - $discountPercent / 100), 2);
        $amountInCents = intval(round($discountedTotal * 100));

        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => env('STRIPE_CURRENCY', 'chf'),
                'metadata' => [
                    'user_app_id' => $request->user_app_id,
                    'service_id' => $request->service_id,
                    'promo_code' => $promoCode?->code ?? 'none',
                ]
            ]);

            // Crear la reserva
            $booking = \App\Models\Booking::create([
                'user_app_id' => $request->user_app_id,
                'service_id' => $request->service_id,
                'price' => $discountedTotal,
                'duration' => $request->duration,
                'address' => $request->address,
                'service_day' => $request->service_day,
                'status' => 'pending',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'additional_details' => $request->additional_details,
                'promo_code_id' => $promoCode?->id,
            ]);

            // Si hay promo válida, aumentar redenciones y registrar en la tabla pivote
            if ($promoCode) {
                $promoCode->increment('redemptions');

                $promoCode->users()->attach($request->user_app_id, [
                    'booking_id' => $booking->id,
                    'used_at' => now()
                ]);
            }

            return response()->json([
                'booking' => $booking,
                'clientSecret' => $paymentIntent->client_secret,
                'promo' => $promoCode,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getByUser($userId)
    {
        $bookings = \App\Models\Booking::with([
            'service.category',           // Categoría del servicio
            'service.professional',       // Profesional que ofrece el servicio
            'promoCode'                   // Código promocional aplicado
        ])
        ->where('user_app_id', $userId)
        ->orderBy('service_day', 'desc')
        ->get();

        return response()->json($bookings);
    }

}
