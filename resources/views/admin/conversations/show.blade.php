@extends('layouts.app')
@section('title', 'Chat #'.$conversation->id)

@section('content')
<a href="{{ route('admin.conversations.index') }}" class="mb-4 inline-flex items-center gap-2 text-blue-600 hover:underline">
    <i data-feather="arrow-left"></i> Volver a conversaciones
</a>

<h2 class="text-xl font-bold mb-4 flex items-center gap-2">
    <i data-feather="message-square"></i>
    Chat #{{ $conversation->id }}
</h2>
<div class="mb-2">
    <strong>Tipo:</strong>
    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
        {{ ucfirst($conversation->type) }}
    </span>
</div>
<div class="mb-6">
    <strong>Participantes:</strong>
    @foreach($conversation->participants as $u)
        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">
            {{ $u->name ?? '' }} {{ $u->last_name ?? '' }} ({{ $u->email ?? '' }})
        </span>
    @endforeach
</div>
<div class="bg-white rounded-xl shadow p-4 max-w-2xl">
    <h3 class="font-semibold mb-4">Mensajes</h3>
    <div class="flex flex-col gap-4">
        @forelse($conversation->messages as $m)
            <div class="flex flex-col @if($m->is_violation) bg-red-100 border border-red-400 rounded @endif p-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-bold">{{ $m->sender->name ?? 'Soporte' }} {{ $m->sender->last_name ?? '' }}</span>
                    <span class="text-xs text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</span>
                    @if($m->is_violation)
                        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded">🚩 Posible violación</span>
                    @endif
                </div>
                <div class="text-gray-800 whitespace-pre-line">{{ $m->content }}</div>
                @if($m->display_content && $m->display_content !== $m->content)
                    <div class="mt-2 text-xs text-gray-500">Mensaje filtrado: {{ $m->display_content }}</div>
                @endif
            </div>
        @empty
            <div class="text-gray-500 italic">No hay mensajes.</div>
        @endforelse
    </div>
</div>
@endsection
