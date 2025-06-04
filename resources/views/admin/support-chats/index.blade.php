@extends('layouts.app')
@section('title', 'Chats de Soporte')

@section('content')
<h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
    <i data-feather="headphones"></i>
    Chats de Soporte
</h1>
<div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <table class="min-w-full">
        <thead>
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Usuario</th>
                <th class="px-3 py-2 text-left">Último mensaje</th>
                <th class="px-3 py-2 text-left">Alertas</th>
                <th class="px-3 py-2 text-left">Acción</th>
            </tr>
        </thead>
        <tbody>
        @forelse($chats as $chat)
            <tr class="border-b hover:bg-blue-50">
                <td class="px-3 py-2">{{ $chat->id }}</td>
                <td class="px-3 py-2">
                    @foreach($chat->participants as $u)
                        @if($u->id !== 1) {{-- 1 es admin/support --}}
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                {{ $u->name ?? '' }} {{ $u->last_name ?? '' }}
                            </span>
                        @endif
                    @endforeach
                </td>
                <td class="px-3 py-2">
                    @if($chat->lastMessage)
                        {{ $chat->lastMessage->created_at->diffForHumans() }}
                        @if($chat->lastMessage->sender_id != 1)
                            {{-- El usuario ha escrito el último --}}
                            <span class="inline-flex items-center gap-1 ml-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded" title="El usuario espera respuesta">
                                <i data-feather="mail" class="w-4 h-4"></i>
                                Pendiente respuesta
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 ml-2 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded" title="Admin respondió el último">
                                <i data-feather="check-circle" class="w-4 h-4"></i>
                                Respondido
                            </span>
                        @endif
                    @else
                        <span class="text-gray-500">Sin mensajes</span>
                    @endif
                </td>
                <td class="px-3 py-2">
                    @if($chat->violations_count > 0)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-xs animate-pulse" title="Posible mensaje inapropiado">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    @else
                        <span class="inline-block bg-green-100 text-green-600 text-xs px-2 py-0.5 rounded">OK</span>
                    @endif
                </td>
                <td class="px-3 py-2">
                    <a href="{{ route('admin.support-chats.show', $chat) }}"
                        class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition">
                        <i data-feather="eye" class="w-4 h-4"></i> Ver chat
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-3 py-6 text-center text-gray-500">No hay chats de soporte.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-6">
        {{ $chats->links() }}
    </div>
</div>
@endsection
