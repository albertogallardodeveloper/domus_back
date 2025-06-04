@extends('layouts.app')
@section('title', 'Nueva categoría de servicio')

@section('content')
<h2 class="text-xl font-bold flex items-center gap-2 mb-4">
    <i data-feather="plus-circle"></i>
    Crear nueva categoría
</h2>
<form action="{{ route('admin.service-categories.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 max-w-lg">
    @csrf
    <div class="mb-4">
        <label class="block font-semibold mb-1">Nombre</label>
        <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required>
        @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Categoría padre</label>
        <select name="parent_id" class="w-full border rounded p-2">
            <option value="">Ninguna</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Imagen</label>
        <input type="file" name="image" class="w-full border rounded p-2">
        @error('image') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="check"></i>
            Guardar categoría
        </button>
        <a href="{{ route('admin.service-categories.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="arrow-left"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
