@extends('layouts.app')
@section('title', 'Pagos Stripe')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i data-feather="credit-card"></i>
        Pagos (Stripe)
    </h1>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-5 flex items-center gap-2">
        <i data-feather="check-circle"></i>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bg-red-100 text-red-800 p-3 rounded mb-5 flex items-center gap-2">
        <i data-feather="alert-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <table class="min-w-full">
        <thead>
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Usuario</th>
                <th class="px-3 py-2 text-left">Servicio</th>
                <th class="px-3 py-2 text-left">Fecha</th>
                <th class="px-3 py-2 text-left">Importe</th>
                <th class="px-3 py-2 text-left">Estado</th>
                <th class="px-3 py-2 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($bookings as $booking)
            <tr class="border-b hover:bg-blue-50">
                <td class="px-3 py-2">{{ $booking->id }}</td>
                <td class="px-3 py-2">
                    {{ $booking->user->name ?? '-' }}
                </td>
                <td class="px-3 py-2">
                    {{ $booking->service->title ?? '-' }}
                </td>
                <td class="px-3 py-2">
                    {{ \Carbon\Carbon::parse($booking->service_day)->format('d/m/Y H:i') }}
                </td>
                <td class="px-3 py-2 font-semibold">
                    {{ number_format($booking->price, 2) }} €
                </td>
                <td class="px-3 py-2">
                    <span class="px-2 py-1 rounded text-xs
                        @if($booking->status == 'paid') bg-green-100 text-green-700
                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-700
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                        @elseif($booking->status == 'refunded') bg-gray-100 text-gray-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                <td class="px-3 py-2">
                    <a href="{{ route('admin.stripe.show', $booking) }}"
                       class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition">
                        <i data-feather="eye" class="w-4 h-4"></i> Detalles
                    </a>
                    @if($booking->status == 'paid')
                        <form action="{{ route('admin.stripe.refund', $booking) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition"
                                onclick="return confirm('¿Reembolsar este pago?')">
                                <i data-feather="rotate-ccw" class="w-4 h-4"></i> Reembolsar
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-6 text-center text-gray-500">No hay pagos.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
