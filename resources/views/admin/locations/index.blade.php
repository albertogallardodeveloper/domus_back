@extends('layouts.app')
@section('title', 'Localizaciones')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i data-feather="map-pin"></i>
        Gestión de localizaciones
    </h1>
    <a href="{{ route('admin.locations.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow transition">
        <i data-feather="plus-circle"></i>
        Nueva localización
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-5 flex items-center gap-2">
        <i data-feather="check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <table class="min-w-full">
        <thead>
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Nombre</th>
                <th class="px-3 py-2 text-left">País</th>
                <th class="px-3 py-2 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($locations as $location)
            <tr class="border-b hover:bg-blue-50">
                <td class="px-3 py-2">{{ $location->id }}</td>
                <td class="px-3 py-2 font-semibold">{{ $location->name }}</td>
                <td class="px-3 py-2">
                    @if($location->country)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">{{ $location->country->name }}</span>
                    @else
                        <span class="inline-block bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded">Sin país</span>
                    @endif
                </td>
                <td class="px-3 py-2">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.locations.edit', $location) }}"
                           class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition" title="Editar">
                            <i data-feather="edit-2" class="w-4 h-4"></i> Editar
                        </a>
                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar localización?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition"
                                    title="Eliminar">
                                <i data-feather="trash-2" class="w-4 h-4"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-3 py-6 text-center text-gray-500">No hay localizaciones registradas.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-6">
        {{ $locations->links() }}
    </div>
</div>
@endsection
