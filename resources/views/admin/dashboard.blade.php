@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-5">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-blue-700 mb-2">¡Bienvenido, {{ Auth::user()->name }}!</h1>
        <p class="text-gray-600">Resumen rápido del estado de DOMUS.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white shadow rounded-xl p-5 flex items-center gap-4">
            <div class="bg-blue-100 text-blue-600 rounded-full p-3">
                <i data-feather="users"></i>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $usersCount ?? '—' }}</div>
                <div class="text-gray-500 text-xs">Usuarios registrados</div>
            </div>
        </div>
        <div class="bg-white shadow rounded-xl p-5 flex items-center gap-4">
            <div class="bg-green-100 text-green-600 rounded-full p-3">
                <i data-feather="calendar"></i>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $bookingsCount ?? '—' }}</div>
                <div class="text-gray-500 text-xs">Reservas</div>
            </div>
        </div>
        <div class="bg-white shadow rounded-xl p-5 flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3">
                <i data-feather="briefcase"></i>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $servicesCount ?? '—' }}</div>
                <div class="text-gray-500 text-xs">Servicios activos</div>
            </div>
        </div>
        <div class="bg-white shadow rounded-xl p-5 flex items-center gap-4">
            <div class="bg-pink-100 text-pink-600 rounded-full p-3">
                <i data-feather="message-square"></i>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $conversationsCount ?? '—' }}</div>
                <div class="text-gray-500 text-xs">Chats activos</div>
            </div>
        </div>
    </div>

    <!-- Gráfica y últimas reservas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-xl p-6 flex flex-col">
            <div class="flex items-center justify-between mb-2">
                <div class="text-lg font-semibold text-blue-700 flex items-center gap-2">
                    <i data-feather="bar-chart-2"></i>
                    Reservas por mes
                </div>
            </div>
            <canvas id="reservasPorMesChart" height="150"></canvas>
        </div>

        <!-- Últimas reservas -->
        <div class="bg-white shadow rounded-xl p-6">
            <div class="text-lg font-semibold text-green-700 flex items-center gap-2 mb-3">
                <i data-feather="calendar"></i>
                Últimas reservas
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left font-bold text-gray-500">Cliente</th>
                        <th class="text-left font-bold text-gray-500">Servicio</th>
                        <th class="text-left font-bold text-gray-500">Fecha</th>
                        <th class="text-left font-bold text-gray-500">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestBookings ?? [] as $booking)
                        <tr class="border-b">
                            <td class="py-1">{{ $booking->userApp->name ?? '-' }}</td>
                            <td class="py-1">{{ $booking->service->title ?? '-' }}</td>
                            <td class="py-1">{{ \Carbon\Carbon::parse($booking->service_day)->format('d/m/Y H:i') }}</td>
                            <td class="py-1">
                                <span class="px-2 py-1 rounded text-xs
                                    @if($booking->status == 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($booking->status == 'confirmed') bg-green-100 text-green-700
                                    @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-2 text-gray-400">No hay reservas recientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Últimos usuarios -->
    <div class="bg-white shadow rounded-xl p-6 mt-6">
        <div class="text-lg font-semibold text-blue-700 flex items-center gap-2 mb-3">
            <i data-feather="user-plus"></i>
            Últimos usuarios registrados
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="text-left font-bold text-gray-500">Nombre</th>
                    <th class="text-left font-bold text-gray-500">Email</th>
                    <th class="text-left font-bold text-gray-500">Tipo</th>
                    <th class="text-left font-bold text-gray-500">Registro</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestUsers ?? [] as $user)
                    <tr class="border-b">
                        <td class="py-1">{{ $user->name }} {{ $user->last_name }}</td>
                        <td class="py-1">{{ $user->email }}</td>
                        <td class="py-1">
                            <span class="px-2 py-1 rounded text-xs 
                                @if($user->is_client && $user->is_professional) bg-purple-100 text-purple-700
                                @elseif($user->is_client) bg-blue-100 text-blue-700
                                @elseif($user->is_professional) bg-green-100 text-green-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                @if($user->is_client && $user->is_professional) Cliente / Profesional
                                @elseif($user->is_client) Cliente
                                @elseif($user->is_professional) Profesional
                                @else Otro
                                @endif
                            </span>
                        </td>
                        <td class="py-1">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-2 text-gray-400">No hay usuarios recientes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('reservasPorMesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Reservas',
                data: @json($reservasPorMes),
                backgroundColor: 'rgba(59, 130, 246, 0.5)', // azul Tailwind
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 1,
                borderRadius: 8,
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
