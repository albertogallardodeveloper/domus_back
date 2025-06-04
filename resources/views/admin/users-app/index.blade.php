@extends('layouts.app')

@section('title', 'Usuarios de la App')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i data-feather="users"></i>
        Usuarios registrados
    </h1>
    <a href="{{ route('admin.users-app.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow transition">
        <i data-feather="user-plus"></i>
        Nuevo usuario
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
                <th class="px-3 py-2 text-left">Email</th>
                <th class="px-3 py-2 text-left">Teléfono</th>
                <th class="px-3 py-2 text-left">Roles</th>
                <th class="px-3 py-2 text-left">Localizaciones</th>
                <th class="px-3 py-2 text-left">Idiomas</th>
                <th class="px-3 py-2 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr class="border-b hover:bg-blue-50">
                <td class="px-3 py-2">{{ $user->id }}</td>
                <td class="px-3 py-2 font-semibold">
                    {{ $user->name }} {{ $user->last_name }}
                </td>
                <td class="px-3 py-2">{{ $user->email }}</td>
                <td class="px-3 py-2">{{ $user->phone_number ?? '—' }}</td>
                <td class="px-3 py-2">
                    @if($user->is_client)
                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"><i data-feather="user"></i> Cliente</span>
                    @endif
                    @if($user->is_professional)
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2 py-1 rounded"><i data-feather="briefcase"></i> Profesional</span>
                    @endif
                    @unless($user->is_client || $user->is_professional)
                        <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded"><i data-feather="minus-circle"></i> Sin rol</span>
                    @endunless
                </td>
                <td class="px-3 py-2">
                    @foreach($user->locations as $location)
                        <span class="inline-block bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded mr-1 mb-1">{{ $location->name }}</span>
                    @endforeach
                </td>
                <td class="px-3 py-2">
                    @foreach($user->languages as $language)
                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded mr-1 mb-1">{{ $language->name }}</span>
                    @endforeach
                </td>
                <td class="px-3 py-2">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users-app.edit', $user) }}"
                           class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition" title="Editar">
                            <i data-feather="edit-2" class="w-4 h-4"></i> Editar
                        </a>
                        <form action="{{ route('admin.users-app.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario?')" class="inline">
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
                <td colspan="8" class="px-3 py-6 text-center text-gray-500">No hay usuarios registrados.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
