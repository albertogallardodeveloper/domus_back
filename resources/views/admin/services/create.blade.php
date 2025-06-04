@extends('layouts.app')
@section('title', 'Nuevo servicio')

@section('content')
<h2 class="text-xl font-bold flex items-center gap-2 mb-4">
    <i data-feather="plus-circle"></i>
    Crear nuevo servicio
</h2>
<form action="{{ route('admin.services.store') }}" method="POST" class="bg-white rounded-xl shadow p-6 max-w-lg">
    @csrf
    <div class="mb-4">
        <label class="block font-semibold mb-1">Título</label>
        <input type="text" name="title" class="w-full border rounded p-2" value="{{ old('title') }}" required>
        @error('title') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Profesional</label>
        <select name="user_app_id" class="w-full border rounded p-2" required>
            <option value="">Selecciona un profesional</option>
            @foreach($professionals as $pro)
                <option value="{{ $pro->id }}" {{ old('user_app_id') == $pro->id ? 'selected' : '' }}>
                    {{ $pro->name }} {{ $pro->last_name }} ({{ $pro->email }})
                </option>
            @endforeach
        </select>
        @error('user_app_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Categoría</label>
        <select name="service_category_id" class="w-full border rounded p-2" required>
            <option value="">Selecciona una categoría</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('service_category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('service_category_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Precio (€)</label>
        <input type="number" name="price" step="0.01" min="0" class="w-full border rounded p-2" value="{{ old('price') }}">
        @error('price') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Descripción</label>
        <textarea name="description" rows="3" class="w-full border rounded p-2">{{ old('description') }}</textarea>
        @error('description') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="check"></i>
            Guardar servicio
        </button>
        <a href="{{ route('admin.services.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="arrow-left"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
