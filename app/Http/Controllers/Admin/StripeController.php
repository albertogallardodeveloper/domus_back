<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

class StripeController extends Controller
{
    // Listado de pagos
    public function index(Request $request)
    {
        $bookings = Booking::with(['user', 'service'])
            ->whereNotNull('stripe_payment_intent_id')
            ->orderByDesc('service_day')
            ->paginate(20);

        return view('admin.stripe.index', compact('bookings'));
    }

    // Detalle de un pago concreto
    public function show($id)
    {
        $booking = Booking::with(['user', 'service'])->findOrFail($id);

        // Info actual de Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $stripePayment = null;
        try {
            $stripePayment = PaymentIntent::retrieve($booking->stripe_payment_intent_id);
        } catch (\Exception $e) {}

        return view('admin.stripe.show', compact('booking', 'stripePayment'));
    }

    // Acción de reembolso
    public function refund($id)
    {
        $booking = Booking::where('id', $id)->firstOrFail();
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $paymentIntent = PaymentIntent::retrieve($booking->stripe_payment_intent_id);
            $chargeId = $paymentIntent->charges->data[0]->id ?? null;

            if (!$chargeId) {
                return back()->with('error', 'No se encontró el cargo asociado.');
            }

            $refund = Refund::create([
                'charge' => $chargeId,
                // 'amount' => ... // Si quieres parcial, añade aquí
            ]);

            // Marcar como reembolsado en tu BD si quieres
            $booking->update(['status' => 'refunded']);

            return back()->with('success', 'Pago reembolsado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al reembolsar: '.$e->getMessage());
        }
    }
}
