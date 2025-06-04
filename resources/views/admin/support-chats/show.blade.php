@extends('layouts.app')
@section('title', 'Soporte Chat #'.$conversation->id)

@section('content')
<a href="{{ route('admin.support-chats.index') }}" class="mb-4 inline-flex items-center gap-2 text-blue-600 hover:underline">
    <i data-feather="arrow-left"></i> Volver a soporte
</a>

<h2 class="text-xl font-bold mb-4 flex items-center gap-2">
    <i data-feather="headphones"></i>
    Chat de Soporte #{{ $conversation->id }}
</h2>
<div class="mb-2">
    <strong>Usuario:</strong>
    @foreach($conversation->participants as $u)
        @if($u->id !== 1)
            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">
                {{ $u->name ?? '' }} {{ $u->last_name ?? '' }} ({{ $u->email ?? '' }})
            </span>
        @endif
    @endforeach
</div>
<div class="bg-white rounded-xl shadow p-4 max-w-2xl mb-8">
    <h3 class="font-semibold mb-4">Mensajes</h3>
    <div class="flex flex-col gap-4">
        @forelse($conversation->messages as $m)
            <div class="flex flex-col @if($m->is_violation) bg-red-100 border border-red-400 rounded @endif p-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-bold">
                        @if($m->sender && $m->sender->id == 1) Soporte @else {{ $m->sender->name ?? 'Desconocido' }} {{ $m->sender->last_name ?? '' }} @endif
                    </span>
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

<form method="POST" action="{{ route('admin.support-chats.reply', $conversation) }}" class="bg-white rounded-xl shadow p-6 max-w-2xl">
    @csrf
    <h4 class="font-semibold mb-2">Enviar respuesta</h4>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    <textarea name="message" rows="3" class="w-full border rounded p-2 mb-4" placeholder="Escribe tu mensaje..."></textarea>
    @error('message') <div class="text-red-600 text-sm mb-2">{{ $message }}</div> @enderror
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded flex items-center gap-2">
        <i data-feather="send"></i>
        Enviar
    </button>
</form>
@endsection
