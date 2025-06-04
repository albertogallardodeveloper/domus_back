<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Administrador DOMUS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        /* Extra glassmorphism */
        .glass {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border-radius: 1.25rem;
            border: 1px solid rgba(255,255,255,0.4);
        }
        .blur-bg {
            position: fixed;
            top: -100px; left: -150px;
            width: 400px; height: 400px;
            background: radial-gradient(ellipse at center, #2563eb77 0%, #0ea5e999 60%, transparent 100%);
            filter: blur(70px);
            z-index: 0;
        }
        .blur-bg-2 {
            position: fixed;
            bottom: -120px; right: -120px;
            width: 340px; height: 340px;
            background: radial-gradient(ellipse at center, #60a5fa66 0%, #3b82f699 60%, transparent 100%);
            filter: blur(60px);
            z-index: 0;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-blue-100 via-white to-blue-200 min-h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Fondo moderno -->
    <div class="blur-bg"></div>
    <div class="blur-bg-2"></div>

    <div class="w-full max-w-md glass shadow-2xl px-8 py-10 relative z-10">
        <!-- Logo circular flotante -->
        <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
            <div class="bg-gradient-to-br from-blue-600 to-sky-400 rounded-full w-20 h-20 flex items-center justify-center shadow-lg border-4 border-white">
                <!-- Usa tu logo aquí si quieres -->
                <i data-feather="lock" class="w-10 h-10 text-white"></i>
            </div>
        </div>
        <h2 class="text-3xl font-extrabold text-center mb-2 mt-10 text-blue-800 tracking-widest">DOMUS</h2>
        <div class="text-center mb-7 text-blue-400 font-semibold">Acceso al panel de administración</div>

        @if($errors->any())
            <div class="mb-5 bg-red-50 text-red-700 border border-red-200 px-3 py-2 rounded text-center flex items-center gap-2">
                <i data-feather="alert-circle" class="w-4 h-4"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block mb-1 text-sm font-semibold text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 transition bg-white/70"
                       placeholder="admin@domus.com" required autofocus>
            </div>
            <div>
                <label class="block mb-1 text-sm font-semibold text-gray-700">Contraseña</label>
                <input type="password" name="password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 transition bg-white/70"
                       placeholder="••••••••" required>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2 accent-blue-600">
                <label for="remember" class="text-sm text-gray-600">Recordarme</label>
            </div>
            <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-sky-400 hover:from-blue-700 hover:to-sky-500 text-white font-bold py-2.5 rounded-xl text-lg shadow transition">
                Entrar
            </button>
        </form>
        <div class="mt-7 text-xs text-gray-400 text-center tracking-wide">v0.0.1 &middot; DOMUS</div>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>
