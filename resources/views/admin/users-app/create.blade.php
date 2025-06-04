@extends('layouts.app')

@section('title', 'Nuevo usuario de la App')

@section('content')
<h2 class="text-xl font-bold flex items-center gap-2 mb-4">
    <i data-feather="user-plus"></i>
    Crear nuevo usuario
</h2>

<form action="{{ route('admin.users-app.store') }}" method="POST" class="bg-white rounded-xl shadow p-6 max-w-2xl">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold mb-1">Nombre</label>
            <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required>
            @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block font-semibold mb-1">Apellidos</label>
            <input type="text" name="last_name" class="w-full border rounded p-2" value="{{ old('last_name') }}" required>
            @error('last_name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" value="{{ old('email') }}" required>
            @error('email') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block font-semibold mb-1">Teléfono</label>
            <input type="text" name="phone_number" class="w-full border rounded p-2" value="{{ old('phone_number') }}">
            @error('phone_number') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block font-semibold mb-1">Contraseña</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
            @error('password') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block font-semibold mb-1">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
        </div>
        <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="is_client" value="1" {{ old('is_client') ? 'checked' : '' }}>
            <label>Cliente</label>
        </div>
        <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="is_professional" value="1" {{ old('is_professional') ? 'checked' : '' }}>
            <label>Profesional</label>
        </div>
        <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="privacy_policy" value="1" required>
            <label>Acepta política de privacidad</label>
        </div>
        <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="terms_conditions" value="1" required>
            <label>Acepta términos y condiciones</label>
        </div>
        <div>
            <label class="block font-semibold mb-1">Idiomas</label>
            <select name="languages[]" multiple class="w-full border rounded p-2">
                @foreach($languages as $language)
                    <option value="{{ $language->id }}">{{ $language->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold mb-1">Localizaciones</label>
            <select name="locations[]" multiple class="w-full border rounded p-2">
                @foreach($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mt-6 flex gap-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="check"></i>
            Guardar usuario
        </button>
        <a href="{{ route('admin.users-app.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded flex items-center gap-2">
            <i data-feather="arrow-left"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
