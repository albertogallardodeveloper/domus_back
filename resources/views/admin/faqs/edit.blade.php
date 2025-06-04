@extends('layouts.app')
@section('title', 'Editar FAQ')

@section('content')
<h2 class="text-xl font-bold flex items-center gap-2 mb-4">
    <i data-feather="edit"></i>
    Editar FAQ
</h2>

<form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="bg-white rounded-xl shadow p-6 max-w-2xl">
    @csrf @method('PUT')
    <div class="mb-4">
        <label class="block font-semibold mb-1">Categoría</label>
        <input type="text" name="category" class="w-full border rounded p-2" value="{{ old('category', $faq->category) }}" required>
        @error('category') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Título</label>
        <input type="text" name="title" class="w-full border rounded p-2" value="{{ old('title', $faq->title) }}" required>
        @error('title') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Descripción</label>
        <textarea name="description" rows="4" class="w-full border rounded p-2" required>{{ old('description', $faq->description) }}</textarea>
        @error('description') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Estado</label>
        <select name="status" class="w-full border rounded p-2" required>
            <option value="Uploaded" {{ old('status', $faq->status) == 'Uploaded' ? 'selected' : '' }}>Subida</option>
            <option value="To be uploaded" {{ old('status', $faq->status) == 'To be uploaded' ? 'selected' : '' }}>Pendiente</option>
        </select>
        @error('status') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="save"></i>
            Guardar cambios
        </button>
        <a href="{{ route('admin.faqs.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="arrow-left"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
