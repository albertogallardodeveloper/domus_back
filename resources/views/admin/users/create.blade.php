@extends('layouts.app')
@section('title', isset($user) ? 'Editar Admin' : 'Nuevo Admin')

@section('content')
<div class="max-w-lg mx-auto bg-white/90 rounded-2xl shadow p-8">
    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
        <i data-feather="shield"></i>
        {{ isset($user) ? 'Editar administrador' : 'Nuevo administrador' }}
    </h2>
    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Contraseña {{ isset($user) ? '(dejar en blanco para no cambiar)' : '' }}</label>
            <input type="password" name="password"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" {{ isset($user) ? '' : 'required' }}>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Confirmar contraseña</label>
            <input type="password" name="password_confirmation"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" {{ isset($user) ? '' : 'required' }}>
        </div>
        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl shadow transition">
                {{ isset($user) ? 'Actualizar' : 'Crear' }}
            </button>
        </div>
    </form>
</div>
@endsection
