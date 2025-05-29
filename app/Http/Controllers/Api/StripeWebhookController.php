<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Stripe recomienda usar el raw body y verificar la firma para seguridad extra
        $payload = @file_get_contents('php://input');
        $event = json_decode($payload, true);

        // Puedes añadir aquí la verificación de la firma de Stripe si lo deseas
        // (Opcional y recomendado en producción)

        if (isset($event['type']) && $event['type'] === 'payment_intent.succeeded') {
            $paymentIntent = $event['data']['object'];
            $paymentIntentId = $paymentIntent['id'];

            // Busca la reserva por payment_intent_id
            $booking = Booking::where('stripe_payment_intent_id', $paymentIntentId)->first();
            if ($booking) {
                $booking->status = 'paid';
                $booking->save();
            }
        }

        // Stripe espera siempre un 200 OK rápido
        return response()->json(['status' => 'success']);
    }
}
