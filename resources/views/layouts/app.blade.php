<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración DOMUS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Alpine.js para los desplegables -->
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg flex flex-col min-h-screen px-4 py-6">
        <div class="flex justify-center mb-5">
            <img src="/assets/logo DOMUS azul electrico transp.png" alt="Logo Domus" class="w-10 h-10" />
        </div>
        <nav class="flex-1">
            <ul class="space-y-1">
                <!-- USUARIOS -->
                <li class="text-xs uppercase text-gray-400 mt-2 mb-1 px-3 tracking-wider">Usuarios</li>
                <li>
                    <a href="{{ route('admin.users-app.index') }}"
                       class="flex items-center px-3 py-2 rounded hover:bg-blue-50 transition
                       {{ request()->is('admin/users-app*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                        <i data-feather="users" class="mr-2"></i>
                        Usuarios App
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.languages.index') }}"
                       class="flex items-center px-3 py-2 rounded hover:bg-blue-50 transition
                       {{ request()->is('admin/languages*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                        <i data-feather="globe" class="mr-2"></i>
                        Idiomas
                    </a>
                </li>

                <!-- UBICACIÓN -->
                <li class="text-xs uppercase text-gray-400 mt-4 mb-1 px-3 tracking-wider">Ubicación</li>
                <li x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="flex items-center w-full px-3 py-2 rounded hover:bg-blue-50 transition text-gray-700 focus:outline-none"
                        :class="open ? 'bg-blue-100 text-blue-700 font-semibold' : ''">
                        <i data-feather="map" class="mr-2"></i>
                        Geografía
                        <svg class="ml-auto w-4 h-4 transition-transform"
                             :class="open ? 'rotate-90' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="ml-7 space-y-1 py-1">
                        <li>
                            <a href="{{ route('admin.countries.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/countries*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="flag" class="w-4 h-4 mr-1"></i> Países
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.locations.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/locations*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="map-pin" class="w-4 h-4 mr-1"></i> Localizaciones
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.addresses.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/addresses*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="navigation" class="w-4 h-4 mr-1"></i> Direcciones
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- SERVICIOS -->
                <li class="text-xs uppercase text-gray-400 mt-4 mb-1 px-3 tracking-wider">Servicios</li>
                <li x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="flex items-center w-full px-3 py-2 rounded hover:bg-blue-50 transition text-gray-700 focus:outline-none"
                        :class="open ? 'bg-blue-100 text-blue-700 font-semibold' : ''">
                        <i data-feather="layers" class="mr-2"></i>
                        Servicios
                        <svg class="ml-auto w-4 h-4 transition-transform"
                             :class="open ? 'rotate-90' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="ml-7 space-y-1 py-1">
                        <li>
                            <a href="{{ route('admin.service-categories.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/service-categories*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="grid" class="w-4 h-4 mr-1"></i> Categorías
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.services.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/services*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="briefcase" class="w-4 h-4 mr-1"></i> Servicios
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/bookings*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="calendar" class="w-4 h-4 mr-1"></i> Reservas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.promo-codes.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/promo-codes*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="percent" class="w-4 h-4 mr-1"></i> Códigos Promo
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.stripe.index') }}"
                            class="flex items-center px-3 py-2 rounded hover:bg-blue-50 transition
                            {{ request()->is('admin/stripe*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="credit-card" class="mr-2"></i>
                                Pagos Stripe
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- SOPORTE Y FAQ -->
                <li class="text-xs uppercase text-gray-400 mt-4 mb-1 px-3 tracking-wider">Atención y Soporte</li>
                <li>
                    <a href="{{ route('admin.faqs.index') }}" class="flex items-center px-3 py-2 rounded hover:bg-blue-50 transition
                        {{ request()->is('admin/faqs*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                        <i data-feather="help-circle" class="mr-2"></i> FAQs
                    </a>
                </li>
                <li x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="flex items-center w-full px-3 py-2 rounded hover:bg-blue-50 transition text-gray-700 focus:outline-none"
                        :class="open ? 'bg-blue-100 text-blue-700 font-semibold' : ''">
                        <i data-feather="message-circle" class="mr-2"></i>
                        Chats
                        <svg class="ml-auto w-4 h-4 transition-transform"
                             :class="open ? 'rotate-90' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="ml-7 space-y-1 py-1">
                        <li>
                            <a href="{{ route('admin.support-chats.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/support-chats*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="headphones" class="w-4 h-4 mr-1"></i> Chats de Soporte
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.conversations.index') }}" class="flex items-center px-2 py-1 rounded hover:bg-blue-50 transition
                                {{ request()->is('admin/conversations*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' }}">
                                <i data-feather="message-square" class="w-4 h-4 mr-1"></i> Chats Privados
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- Sesión y versión -->
        <div class="mt-10">
            <div class="flex justify-between items-center">
                @auth
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center gap-2 text-left text-red-600 px-3 py-2 rounded hover:bg-red-50 transition">
                        <i data-feather="log-out"></i>
                        Cerrar sesión
                    </button>
                </form>
                @endauth
                <div class="text-gray-400 text-sm">
                    <span class="ml-2">v0.0.1</span>
                </div>
            </div>
        </div>
    </aside>
    <!-- Main content -->
    <main class="flex-1 bg-gray-100">
        <header class="bg-white shadow py-4 px-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-blue-700">@yield('title', 'Panel de Administración')</h1>
            @auth
            <div class="text-gray-600">
                {{ Auth::user()->name }}
            </div>
            @endauth
        </header>
        <section class="px-6 py-8">
            @yield('content')
        </section>
    </main>
    <script>
        feather.replace();
    </script>
    @stack('scripts')
</body>
</html>
