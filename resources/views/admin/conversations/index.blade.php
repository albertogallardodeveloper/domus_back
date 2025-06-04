@extends('layouts.app')
@section('title', 'Conversaciones')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i data-feather="message-square"></i>
        Conversaciones (chat)
    </h1>
</div>

<div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <table class="min-w-full">
        <thead>
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Tipo</th>
                <th class="px-3 py-2 text-left">Participantes</th>
                <th class="px-3 py-2 text-left">Último mensaje</th>
                <th class="px-3 py-2 text-left">Acción</th>
            </tr>
        </thead>
 <tbody>
@forelse($conversations as $c)
    <tr class="border-b hover:bg-blue-50">
        <td class="px-3 py-2 flex items-center gap-2">
            {{ $c->id }}
            @if($c->violations_count > 0)
                <span title="Posible mensaje inapropiado" class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-xs animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            @endif
        </td>
        <td class="px-3 py-2">
            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                {{ ucfirst($c->type ?? '-') }}
            </span>
        </td>
        <td class="px-3 py-2">
            @foreach($c->participants as $u)
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">
                    {{ $u->name ?? '' }} {{ $u->last_name ?? '' }}
                </span>
            @endforeach
        </td>
        <td class="px-3 py-2">
            {{ $c->updated_at ? $c->updated_at->diffForHumans() : '-' }}
        </td>
        <td class="px-3 py-2">
            <a href="{{ route('admin.conversations.show', $c) }}"
                class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition">
                <i data-feather="eye" class="w-4 h-4"></i> Ver chat
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-3 py-6 text-center text-gray-500">No hay chats.</td>
    </tr>
@endforelse
</tbody>

    </table>
    <div class="mt-6">
        {{ $conversations->links() }}
    </div>
</div>
@endsection
