@extends('layouts.app')
@section('title', 'Nuevo país')

@section('content')
<h2 class="text-xl font-bold flex items-center gap-2 mb-4">
    <i data-feather="plus-circle"></i>
    Crear nuevo país
</h2>

<form action="{{ route('admin.countries.store') }}" method="POST" class="bg-white rounded-xl shadow p-6 max-w-lg">
    @csrf
    <div class="mb-4">
        <label class="block font-semibold mb-1">Nombre</label>
        <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required>
        @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Código ISO</label>
        <input type="text" name="iso_code" class="w-full border rounded p-2" value="{{ old('iso_code') }}" required>
        @error('iso_code') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Código teléfono</label>
        <input type="text" name="phone_code" class="w-full border rounded p-2" value="{{ old('phone_code') }}">
        @error('phone_code') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="check"></i>
            Guardar país
        </button>
        <a href="{{ route('admin.countries.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="arrow-left"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
