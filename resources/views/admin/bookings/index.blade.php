@extends('layouts.app')
@section('title', 'Reservas')

@section('content')
<h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
    <i data-feather="calendar"></i>
    Reservas (Bookings)
</h1>

<div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="user" value="{{ request('user') }}" placeholder="Buscar usuario..." class="border rounded px-2 py-1">
        <select name="status" class="border rounded px-2 py-1">
            <option value="">Todos los estados</option>
            <option value="pending" @selected(request('status')=='pending')>Pendiente</option>
            <option value="confirmed" @selected(request('status')=='confirmed')>Confirmada</option>
            <option value="cancelled" @selected(request('status')=='cancelled')>Cancelada</option>
        </select>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded">Filtrar</button>
    </form>
    <table class="min-w-full text-sm">
        <thead>
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Usuario</th>
                <th class="px-3 py-2 text-left">Servicio</th>
                <th class="px-3 py-2 text-left">Profesional</th>
                <th class="px-3 py-2 text-left">Fecha</th>
                <th class="px-3 py-2 text-left">Dirección</th>
                <th class="px-3 py-2 text-left">Precio</th>
                <th class="px-3 py-2 text-left">Estado</th>
                <th class="px-3 py-2 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $b)
            <tr class="border-b hover:bg-blue-50">
                <td class="px-3 py-2">{{ $b->id }}</td>
                <td class="px-3 py-2">
                    {{ $b->userApp->name ?? '' }} {{ $b->userApp->last_name ?? '' }}
                </td>
                <td class="px-3 py-2">{{ $b->service->title ?? '' }}</td>
                <td class="px-3 py-2">
                    {{ $b->service->professional->name ?? '' }} {{ $b->service->professional->last_name ?? '' }}
                </td>
                <td class="px-3 py-2">{{ $b->service_day ? \Carbon\Carbon::parse($b->service_day)->format('d/m/Y H:i') : '-' }}</td>
                <td class="px-3 py-2">{{ $b->address }}</td>
                <td class="px-3 py-2">{{ number_format($b->price, 2) }} €</td>
                <td class="px-3 py-2">
                    @if($b->status == 'pending')
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pendiente</span>
                    @elseif($b->status == 'confirmed')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Confirmada</span>
                    @elseif($b->status == 'cancelled')
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelada</span>
                    @else
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">{{ ucfirst($b->status) }}</span>
                    @endif
                </td>
                <td class="px-3 py-2 flex gap-1">
                    <a href="{{ route('admin.bookings.edit', $b) }}"
                        class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">
                        <i data-feather="edit-2" class="w-4 h-4"></i> Editar
                    </a>
                    @if($b->status !== 'cancelled')
                    <form action="{{ route('admin.bookings.cancel', $b) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres cancelar esta reserva?')" class="inline-block">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs transition">
                            <i data-feather="slash" class="w-4 h-4"></i> Cancelar
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.bookings.destroy', $b) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres borrar esta reserva?')" class="inline-block">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 bg-gray-400 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs transition">
                            <i data-feather="trash-2" class="w-4 h-4"></i> Borrar
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="px-3 py-6 text-center text-gray-500">No hay reservas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
