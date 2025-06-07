<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\UserApp;
use App\Models\Review;
use App\Models\PromoCode;
use App\Models\BookingIssue;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Carbon\Carbon;

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

        $service = Service::findOrFail($request->service_id);
        $pricePerHour = $service->price;
        $hours = $request->duration / 60;
        $subtotal = $pricePerHour * $hours;

        $promoCode = null;
        $discountPercent = 0;

        if ($request->filled('promo_code')) {
            $promoCode = PromoCode::where('code', strtoupper($request->promo_code))
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
            \Log::info('Clave Stripe usada:', ['clave' => env('STRIPE_SECRET')]);

            Stripe::setApiKey(env('STRIPE_SECRET'));

            // === NUEVO: Stripe Connect con comisión ===
            // Suponemos que el servicio tiene relación ->professional y este tiene stripe_account_id
            $professional = $service->professional;

            if (! $professional || ! $professional->stripe_account_id) {
                return response()->json(['error' => 'El profesional no tiene cuenta Stripe conectada.'], 422);
            }

            $platformFee = intval($amountInCents * 0.10); // 10% de comisión

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => env('STRIPE_CURRENCY', 'chf'),
                'application_fee_amount' => $platformFee,
                'transfer_data' => [
                    'destination' => $professional->stripe_account_id,
                ],
                'metadata' => [
                    'user_app_id' => $request->user_app_id,
                    'service_id' => $request->service_id,
                    'promo_code' => $promoCode?->code ?? 'none',
                ]
            ]);

            $booking = Booking::create([
                'user_app_id' => $request->user_app_id,
                'service_id' => $request->service_id,
                'price' => $discountedTotal,
                'duration' => $request->duration,
                'address' => $request->address,
                'service_day' => $request->service_day,
                'status' => 'pending',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'platform_fee' => $platformFee,
                'additional_details' => $request->additional_details,
                'promo_code_id' => $promoCode?->id,
            ]);

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
        $bookings = Booking::with([
            'service.category',
            'service.professional',
            'promoCode',
            'review'
        ])
        ->where('user_app_id', $userId)
        ->orderBy('service_day', 'desc')
        ->get();

        return response()->json($bookings);
    }

    public function confirmService(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'confirmed') {
            return response()->json(['error' => 'Este servicio no puede ser confirmado.'], 400);
        }

        if ($booking->confirmed_by_user) {
            return response()->json(['message' => 'Ya fue confirmado.'], 200);
        }

        if (now()->lt($booking->service_day)) {
            return response()->json(['error' => 'No puedes confirmar antes de la fecha del servicio.'], 400);
        }

        $booking->confirmed_by_user = true;
        $booking->save();

        if ($request->has('rating') && $request->has('comment')) {
            Review::create([
                'booking_id'   => $booking->id,
                'service_id'   => $booking->service_id,
                'user_app_id'  => $booking->user_app_id,
                'rating'       => $request->rating,
                'comment'      => $request->comment,
            ]);
        }

        return response()->json(['message' => 'Servicio confirmado correctamente.']);
    }

    public function reportIssue(Request $request, $id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                \Log::error('Usuario no autenticado al reportar problema.');
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $request->validate(['message' => 'required|string']);

            $booking = Booking::findOrFail($id);
            $booking->has_issue = true;
            $booking->save();

            BookingIssue::create([
                'booking_id' => $booking->id,
                'user_app_id' => $user->id,
                'message' => $request->message,
            ]);

            // Buscar conversación solo si existe una para este booking
            $conversation = \App\Models\Conversation::where('type', 'support')
                ->where('booking_id', $booking->id)
                ->first();

            if (!$conversation) {
                $conversation = \App\Models\Conversation::create([
                    'type' => 'support',
                    'booking_id' => $booking->id,
                ]);

                $conversation->participants()->syncWithoutDetaching([$user->id, 1]);

                // Mensaje del sistema
                \App\Models\Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => null,
                    'content' => 'Estamos revisando tu solicitud, en breves nos pondremos en contacto contigo.',
                ]);
            }

            // Mensaje del usuario
            \App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'content' => $request->message,
            ]);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al reportar problema: ' . $e->getMessage());
            return response()->json([
                'error' => 'No se pudo reportar el problema. Intenta de nuevo.'
            ], 500);
        }
        
    }

    public function getSupportConversation($id)
    {
        Log::debug('🔍 ID recibido en getSupportConversation', ['id' => $id]);

        try {
            $user = auth()->user();
            $booking = Booking::findOrFail($id);

            $conversation = \App\Models\Conversation::where('type', 'support')
                ->where('booking_id', $booking->id)
                ->whereHas('participants', fn($q) => $q->where('user_app_id', $user->id))
                ->first();

            if (!$conversation) {
                return response()->json(['error' => 'No se encontró la conversación.'], 404);
            }

            return response()->json(['conversation_id' => $conversation->id]);
        } catch (\Throwable $e) {
            \Log::error('Error obteniendo conversación: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno.'], 500);
        }
    }

    public function cancel(Request $request, Booking $booking)
    {
        Log::info('Intentando cancelar reserva', [
            'booking_id'     => $booking->id,
            'user_id'        => auth()->id(),
            'booking_status' => $booking->status,
            'booking_time'   => $booking->service_day,
        ]);

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Booking already cancelled.'], 400);
        }

        $now = Carbon::now();
        $serviceTime = Carbon::parse($booking->service_day);

        if ($now->greaterThan($serviceTime)) {
            return response()->json(['message' => 'Service time already passed. Cannot cancel.'], 400);
        }

        $diffHours = $now->diffInHours($serviceTime, false);
        $refundPercent = 0;

        if ($diffHours > 24) $refundPercent = 100;
        elseif ($diffHours > 4) $refundPercent = 50;
        elseif ($diffHours > 1) $refundPercent = 20;

        $refundAmount = round($booking->price * ($refundPercent / 100), 2);

        Log::info('Cálculo de reembolso', [
            'booking_id'     => $booking->id,
            'diff_hours'     => $diffHours,
            'refund_percent' => $refundPercent,
            'refund_amount'  => $refundAmount,
        ]);

        if (!$booking->stripe_payment_intent_id) {
            Log::error('Falta stripe_payment_intent_id en la reserva', ['booking_id' => $booking->id]);
            return response()->json(['message' => 'No payment intent found for this booking.'], 400);
        }

        if ($booking->refund_id) {
            Log::warning('La reserva ya tiene un reembolso asociado', ['booking_id' => $booking->id, 'refund_id' => $booking->refund_id]);
            return response()->json(['message' => 'Booking was already refunded.'], 400);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $refund = Refund::create([
                'payment_intent' => $booking->stripe_payment_intent_id,
                'amount'         => intval($refundAmount * 100), // en centavos
            ]);

            $booking->update([
                'status'        => 'cancelled',
                'refund_amount' => $refundAmount,
                'cancelled_at'  => now(),
                'refund_id'     => $refund->id,
            ]);

            return response()->json([
                'message'        => 'Booking cancelled and refunded successfully.',
                'refund_percent' => $refundPercent,
                'refund_amount'  => $refundAmount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear el reembolso en Stripe', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Stripe refund failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getOrCreateUserChat(Request $request)
    {
        \Log::info('🟢 Entrando en getOrCreateUserChat');

        try {
            $user = auth()->user();

            $request->validate([
                'other_user_id' => 'required|exists:users_app,id',
            ]);

            $otherUserId = $request->other_user_id;

            // Evitar chat consigo mismo
            if ($user->id == $otherUserId) {
                return response()->json(['error' => 'No puedes chatear contigo mismo.'], 400);
            }

            // Buscar si ya existe conversación privada entre ambos
            $conversation = \App\Models\Conversation::where('type', 'user')
                ->whereHas('participants', fn($q) => $q->where('user_app_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_app_id', $otherUserId))
                ->first();

            // Si no existe, la creamos
            if (!$conversation) {
                $conversation = \App\Models\Conversation::create([
                    'type' => 'user',
                ]);

                $conversation->participants()->syncWithoutDetaching([$user->id, $otherUserId]);

                // Mensaje del sistema opcional
                \App\Models\Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => null,
                    'content' => '📢 Chat iniciado entre ambos usuarios.',
                ]);
            }

            return response()->json([
                'conversation_id' => $conversation->id,
            ]);
        } catch (\Throwable $e) {
            \Log::error('❌ Error al obtener o crear conversación usuario-usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno.'], 500);
        }
    }

    public function acceptBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json(['error' => 'Solo se pueden aceptar reservas pendientes.'], 400);
        }

        $booking->status = 'confirmed';
        $booking->save();

        return response()->json(['message' => 'Reserva aceptada correctamente.']);
    }

    public function rejectBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json(['error' => 'Solo se pueden rechazar reservas pendientes.'], 400);
        }

        $booking->status = 'rejected';
        $booking->save();

        return response()->json(['message' => 'Reserva rechazada correctamente.']);
    }

}
