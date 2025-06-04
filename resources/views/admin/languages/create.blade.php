@extends('layouts.app')
@section('title', 'Nuevo idioma')

@section('content')
<div class="flex items-center gap-2 mb-8">
    <a href="{{ route('admin.languages.index') }}" class="text-blue-600 hover:underline flex items-center gap-1">
        <i data-feather="arrow-left"></i> Volver
    </a>
</div>

<h1 class="text-2xl font-bold flex items-center gap-2 mb-6">
    <i data-feather="globe"></i> Nuevo idioma
</h1>

@if($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-5 flex items-center gap-2">
        <i data-feather="alert-triangle"></i>
        <span>Corrige los errores e inténtalo de nuevo.</span>
    </div>
@endif

<form action="{{ route('admin.languages.store') }}" method="POST"
      class="max-w-lg bg-white rounded-xl shadow p-6 space-y-6">
    @csrf
    <div>
        <label class="block font-semibold mb-1">Código</label>
        <input name="code" value="{{ old('code') }}"
               class="form-input w-full border-gray-300 rounded px-3 py-2" required autofocus>
        @error('code') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="block font-semibold mb-1">Nombre</label>
        <input name="name" value="{{ old('name') }}"
               class="form-input w-full border-gray-300 rounded px-3 py-2" required>
        @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded shadow">
            Crear idioma
        </button>
        <a href="{{ route('admin.languages.index') }}" class="ml-4 text-gray-600 hover:underline">Cancelar</a>
    </div>
</form>
@endsection
