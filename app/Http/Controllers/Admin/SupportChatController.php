<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportChatController extends Controller
{
public function index()
{
    $chats = \App\Models\Conversation::with(['participants', 'lastMessage.sender'])
        ->where('type', 'support')
        ->withCount(['messages as violations_count' => function($q){
            $q->where('is_violation', true);
        }])
        ->orderByDesc('violations_count')
        ->orderByDesc('updated_at')
        ->paginate(30);

    return view('admin.support-chats.index', compact('chats'));
}



    public function show(Conversation $conversation)
    {
        $conversation->load(['participants', 'messages.sender']);
        return view('admin.support-chats.show', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $admin = Auth::user();

        // Si tu modelo admin es User y no UserApp, pon el ID 1 como admin soporte
        $adminUserAppId = 1; // o mapéalo si tienes el admin real en users_app

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $adminUserAppId,
            'content'         => $request->message,
            'display_content' => $request->message,
            'is_violation'    => false,
        ]);

        return redirect()->route('admin.support-chats.show', $conversation)
            ->with('success', 'Mensaje enviado al usuario.');
    }
}
