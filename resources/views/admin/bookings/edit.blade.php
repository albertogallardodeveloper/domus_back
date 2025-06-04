@extends('layouts.app')
@section('title', 'Editar Reserva')

@section('content')
<div class="mb-6 flex items-center gap-2">
    <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:underline flex items-center gap-1">
        <i data-feather="arrow-left"></i> Volver al listado
    </a>
</div>
<h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
    <i data-feather="edit-2"></i>
    Editar Reserva #{{ $booking->id }}
</h1>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl mx-auto">
    <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 mb-1 font-semibold">Usuario:</label>
            <div class="px-3 py-2 bg-gray-100 rounded">{{ $booking->userApp->name ?? '' }} {{ $booking->userApp->last_name ?? '' }} (ID: {{ $booking->userApp->id }})</div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1 font-semibold">Servicio:</label>
            <div class="px-3 py-2 bg-gray-100 rounded">
                {{ $booking->service->title ?? '' }} 
                @if($booking->service->category)
                    <span class="ml-2 text-gray-500 text-xs">({{ $booking->service->category->name }})</span>
                @endif
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1 font-semibold">Profesional:</label>
            <div class="px-3 py-2 bg-gray-100 rounded">
                {{ $booking->service->professional->name ?? '' }} {{ $booking->service->professional->last_name ?? '' }}
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1 font-semibold">Dirección:</label>
            <input type="text" name="address" value="{{ old('address', $booking->address) }}" class="border px-3 py-2 rounded w-full" readonly>
        </div>

        <div class="mb-4 flex gap-4">
            <div class="flex-1">
                <label class="block text-gray-700 mb-1 font-semibold">Fecha y hora:</label>
                <input type="text" value="{{ \Carbon\Carbon::parse($booking->service_day)->format('d/m/Y H:i') }}" class="border px-3 py-2 rounded w-full" readonly>
            </div>
            <div class="flex-1">
                <label class="block text-gray-700 mb-1 font-semibold">Duración (min):</label>
                <input type="text" value="{{ $booking->duration }}" class="border px-3 py-2 rounded w-full" readonly>
            </div>
        </div>

        <div class="mb-4 flex gap-4">
            <div class="flex-1">
                <label class="block text-gray-700 mb-1 font-semibold">Precio (€):</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $booking->price) }}" class="border px-3 py-2 rounded w-full">
            </div>
            <div class="flex-1">
                <label class="block text-gray-700 mb-1 font-semibold">Estado:</label>
                <select name="status" class="border px-3 py-2 rounded w-full">
                    <option value="pending" @selected($booking->status=='pending')>Pendiente</option>
                    <option value="confirmed" @selected($booking->status=='confirmed')>Confirmada</option>
                    <option value="cancelled" @selected($booking->status=='cancelled')>Cancelada</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1 font-semibold">Detalles adicionales:</label>
            <textarea class="border px-3 py-2 rounded w-full" rows="2" readonly>{{ $booking->additional_details }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1 font-semibold">Código promocional:</label>
            <input type="text" value="{{ $booking->promoCode->code ?? '-' }}" class="border px-3 py-2 rounded w-full" readonly>
        </div>

        <div class="mb-6 flex gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded font-semibold flex items-center gap-1">
                <i data-feather="arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-bold flex items-center gap-2">
                <i data-feather="save"></i> Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection
