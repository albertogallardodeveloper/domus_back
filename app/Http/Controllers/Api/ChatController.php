<?php

namespace App\Http\Controllers\Api;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function getOrCreatePrivateConversation(Request $request)
    {
        try {
            $user = auth()->user();
            \Log::info('🔐 Usuario autenticado:', ['user_id' => $user->id]);

            $otherUserId = $request->input('other_user_id');
            \Log::info('➡️ ID del otro usuario recibido:', ['other_user_id' => $otherUserId]);

            if (!$otherUserId || $user->id === (int) $otherUserId) {
                \Log::warning('❌ ID inválido para conversación privada', [
                    'user_id' => $user->id,
                    'other_user_id' => $otherUserId,
                ]);
                return response()->json(['error' => 'ID de usuario inválido'], 400);
            }

            // Buscar conversación existente
            $conversation = \App\Models\Conversation::where('type', 'private')
                ->whereHas('participants', fn($q) => $q->where('user_app_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_app_id', $otherUserId))
                ->first();

            if ($conversation) {
                \Log::info('📦 Conversación existente encontrada:', ['conversation_id' => $conversation->id]);
            } else {
                \Log::info('🆕 No existe conversación, creando una nueva.');

                $conversation = \App\Models\Conversation::create([
                    'type' => 'private',
                ]);

                $conversation->participants()->syncWithoutDetaching([$user->id, $otherUserId]);

                \Log::info('✅ Conversación creada:', ['conversation_id' => $conversation->id]);
            }

            return response()->json(['conversation_id' => $conversation->id]);

        } catch (\Throwable $e) {
            \Log::error('💥 Error creando conversación privada: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al crear la conversación'], 500);
        }
    }

    public function getOrCreateSupportConversation(Request $request)
    {
        $userId = $request->input('user_app_id');

        $conversation = Conversation::where('type', 'support')
            ->whereHas('participants', fn($q) => $q->where('user_app_id', $userId))
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(['type' => 'support']);
            $conversation->participants()->attach([$userId, 1]); // 1 = admin
        }

        // Cargar mensajes con el remitente (sender)
        $conversation->load(['messages.sender']);

        return response()->json($conversation);
    }

 public function sendMessage(Request $request)
{
    $data = $request->validate([
        'conversation_id'   => 'required|exists:conversations,id',
        'sender_id'         => 'nullable|exists:users_app,id',
        'content'           => 'required|string',   // 🟡 mensaje original
        'display_content'   => 'nullable|string',   // 🟢 mensaje censurado
        'is_violation'      => 'nullable|boolean',  // 🔴 flag
    ]);

$message = Message::create([
    'conversation_id' => $data['conversation_id'],
    'sender_id' => $data['sender_id'],
    'content' => $data['content'], // ← texto original
    'display_content' => $data['display_content'] ?? $data['content'], // ← texto filtrado
    'is_violation' => $data['is_violation'] ?? false,
]);


    return response()->json($message->load('sender'));
}

public function getSupportConversationForBooking($bookingId)
{
    try {
        $booking = \App\Models\Booking::with('userApp')->findOrFail($bookingId);
        $userAppId = $booking->userApp->id;

        $conversation = Conversation::where('type', 'support')
            ->whereHas('participants', fn($q) => $q->where('user_app_id', $userAppId))
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'support',
                'booking_id' => $bookingId, // 👈 Esto es crucial
            ]);
            $conversation->participants()->attach([$userAppId, 1]); // 1 = soporte/admin
        }

        $conversation->load(['messages.sender']);

        return response()->json($conversation);
    } catch (\Throwable $e) {
        \Log::error('Error al obtener conversación de soporte: ' . $e->getMessage());
        return response()->json(['error' => 'No se pudo obtener la conversación'], 500);
    }
}


    public function getMessages($conversationId)
    {
        $conversation = Conversation::with('messages.sender')->findOrFail($conversationId);
        return response()->json($conversation->messages);
    }
}
