@extends('layouts.app')
@section('title', 'Nuevo código promocional')

@section('content')
<div class="flex items-center gap-2 mb-8">
    <a href="{{ route('admin.promo-codes.index') }}" class="text-blue-600 hover:underline flex items-center gap-1">
        <i data-feather="arrow-left"></i> Volver
    </a>
</div>

<h1 class="text-2xl font-bold flex items-center gap-2 mb-6">
    <i data-feather="percent"></i> Nuevo código promocional
</h1>

@if($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-5 flex items-center gap-2">
        <i data-feather="alert-triangle"></i>
        <span>Corrige los errores e inténtalo de nuevo.</span>
    </div>
@endif

<form action="{{ route('admin.promo-codes.store') }}" method="POST"
      class="max-w-lg bg-white rounded-xl shadow p-6 space-y-6">
    @csrf
    <div>
        <label class="block font-semibold mb-1">Código</label>
        <input name="code" value="{{ old('code') }}"
               class="form-input w-full border-gray-300 rounded px-3 py-2" required autofocus>
        @error('code') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="block font-semibold mb-1">Descuento (%)</label>
        <input name="discount_percent" type="number" min="1" max="100"
               value="{{ old('discount_percent') }}"
               class="form-input w-full border-gray-300 rounded px-3 py-2" required>
        @error('discount_percent') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="block font-semibold mb-1">Fecha de expiración</label>
        <input name="expires_at" type="date"
               value="{{ old('expires_at') }}"
               class="form-input w-full border-gray-300 rounded px-3 py-2">
        @error('expires_at') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="block font-semibold mb-1">Redenciones máximas</label>
        <input name="max_redemptions" type="number" min="1"
               value="{{ old('max_redemptions') }}"
               class="form-input w-full border-gray-300 rounded px-3 py-2">
        @error('max_redemptions') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
    <div class="flex items-center gap-2">
        <input name="active" type="checkbox" class="form-checkbox" value="1"
               {{ old('active', true) ? 'checked' : '' }}>
        <label>Activo</label>
    </div>
    <div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded shadow">
            Crear código
        </button>
        <a href="{{ route('admin.promo-codes.index') }}" class="ml-4 text-gray-600 hover:underline">Cancelar</a>
    </div>
</form>
@endsection
