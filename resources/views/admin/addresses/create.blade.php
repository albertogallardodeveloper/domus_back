@extends('layouts.app')
@section('title', 'Nueva dirección')

@section('content')
<h2 class="text-xl font-bold flex items-center gap-2 mb-4">
    <i data-feather="plus-circle"></i>
    Crear nueva dirección
</h2>

<form action="{{ route('admin.addresses.store') }}" method="POST" class="bg-white rounded-xl shadow p-6 max-w-lg">
    @csrf
    <div class="mb-4">
        <label class="block font-semibold mb-1">Dirección</label>
        <input type="text" name="address" class="w-full border rounded p-2" value="{{ old('address') }}" required>
        @error('address') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="block font-semibold mb-1">Asignar a usuarios (opcional)</label>
        <select name="users[]" class="w-full border rounded p-2" multiple>
            @foreach($users as $user)
                <option value="{{ $user->id }}">
                    {{ $user->name }} {{ $user->last_name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="check"></i>
            Guardar dirección
        </button>
        <a href="{{ route('admin.addresses.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="arrow-left"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
