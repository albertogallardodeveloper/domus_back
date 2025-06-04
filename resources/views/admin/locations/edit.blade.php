@extends('layouts.app')
@section('title', 'Editar localización')

@section('content')
<h2 class="text-xl font-bold flex items-center gap-2 mb-4">
    <i data-feather="edit"></i>
    Editar localización
</h2>

<form action="{{ route('admin.locations.update', $location) }}" method="POST" class="bg-white rounded-xl shadow p-6 max-w-lg">
    @csrf @method('PUT')
    <div class="mb-4">
        <label class="block font-semibold mb-1">Nombre</label>
        <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name', $location->name) }}" required>
        @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">País</label>
        <select name="country_id" class="w-full border rounded p-2" required>
            <option value="">Selecciona un país</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ old('country_id', $location->country_id) == $country->id ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
        @error('country_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="save"></i>
            Guardar cambios
        </button>
        <a href="{{ route('admin.locations.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="arrow-left"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
