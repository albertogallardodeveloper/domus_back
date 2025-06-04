<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Admin DOMUS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow">
        <h2 class="text-2xl font-bold text-center mb-6">Panel de Administración DOMUS</h2>
        @if($errors->any())
            <div class="mb-4 text-red-600 text-center">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                    required autofocus>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Contraseña</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                    required>
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-sm">Recordarme</label>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                Entrar
            </button>
        </form>
    </div>
</body>
</html>
