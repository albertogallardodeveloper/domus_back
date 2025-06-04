<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;

class ConversationController extends Controller
{
public function index()
{
    $conversations = \App\Models\Conversation::with(['participants'])
        ->where('type', 'private')
        ->withCount(['messages as violations_count' => function($q){
            $q->where('is_violation', true);
        }])
        ->orderByDesc('violations_count')
        ->orderByDesc('updated_at')
        ->paginate(30);

    return view('admin.conversations.index', compact('conversations'));
}



    public function show(Conversation $conversation)
    {
        $conversation->load(['participants', 'messages.sender']);
        return view('admin.conversations.show', compact('conversation'));
    }
}
