<!DOCTYPE html>
<html lang="es" x-data="app()" x-init="init()" :class="{ 'dark': darkMode }">
<head>
  <meta charset="UTF-8">
  <title>Panel DOMUS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            navy: {
              50: '#f0f4ff', 100: '#dce5ff', 200: '#b5c8ff', 300: '#8da9ff',
              400: '#6188ff', 500: '#3e68f2', 600: '#2f52c7', 700: '#263f9e',
              800: '#1e2f75', 900: '#15214d',
            },
            grayish: {
              50: '#f9fafb', 100: '#f3f4f6', 200: '#e5e7eb', 300: '#d1d5db',
              400: '#9ca3af', 500: '#6b7280', 600: '#4b5563', 700: '#374151',
              800: '#1f2937', 900: '#111827',
            }
          }
        }
      }
    }
  </script>
  <style>
    [x-cloak] { display: none; }
    /* Menú sidebar ocupa todo el alto y nunca hace scroll */
    aside {
      height: 100vh;
      min-height: 100vh;
      max-height: 100vh;
      position: sticky;
      top: 0;
      flex-shrink: 0;
      z-index: 10;
      display: flex;
      flex-direction: column;
    }
    /* Estilo hover claro/oscuro */
    .sidebar-link {
      @apply flex items-center gap-3 p-2 rounded-lg font-medium transition-colors duration-150 select-none cursor-pointer;
    }
    .sidebar-link:hover {
      @apply bg-navy-100 dark:bg-grayish-700 text-navy-800 dark:text-white;
    }
    /* Encabezados de sección */
    .sidebar-section {
      @apply uppercase text-xs tracking-wider text-navy-400 dark:text-grayish-400 pl-2 mb-1 mt-6;
    }
  </style>
</head>
<body class="bg-navy-50 text-navy-900 dark:bg-grayish-900 dark:text-grayish-100 transition-colors duration-300" x-cloak>

<div class="flex min-h-screen">

  <!-- SIDEBAR -->
  <aside class="w-64 bg-navy-800 dark:bg-grayish-800 text-white shadow-xl">
    <!-- TOP: Branding y modo oscuro/claro -->
    <div class="flex flex-col gap-2 items-center justify-center h-20 border-b border-navy-700 dark:border-grayish-700">
      <span class="font-bold text-2xl tracking-wider">DOMUS</span>
      <button
        @click="toggleDark()"
        class="flex items-center gap-1 text-xs px-2 py-1 rounded bg-navy-700 hover:bg-navy-600 dark:bg-grayish-700 dark:hover:bg-grayish-600 transition"
        :title="darkMode ? 'Modo claro' : 'Modo oscuro'">
        <i :data-feather="darkMode ? 'sun' : 'moon'" class="w-4 h-4"></i>
        <span x-text="darkMode ? 'Claro' : 'Oscuro'"></span>
      </button>
    </div>

    <!-- MENU SECCIONES -->
    <nav class="flex-1 px-4 pt-6 pb-2 overflow-hidden">
      <div>
        <div class="sidebar-section">General</div>
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link"
           @click.prevent="navigate('{{ route('admin.dashboard') }}')">
          <i data-feather="home" class="w-5 h-5"></i>
          <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.support-chats.index') }}"
           class="sidebar-link"
           @click.prevent="navigate('{{ route('admin.support-chats.index') }}')">
          <i data-feather="help-circle" class="w-5 h-5"></i>
          <span>Soporte</span>
        </a>
      </div>
      <div>
        <div class="sidebar-section">Usuarios</div>
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link"
           @click.prevent="navigate('{{ route('admin.users.index') }}')">
          <i data-feather="users" class="w-5 h-5"></i>
          <span>Usuarios Web</span>
        </a>
        <a href="{{ route('admin.users-app.index') }}"
           class="sidebar-link"
           @click.prevent="navigate('{{ route('admin.users-app.index') }}')">
          <i data-feather="smartphone" class="w-5 h-5"></i>
          <span>Usuarios App</span>
        </a>
        <a href="{{ route('admin.languages.index') }}"
           class="sidebar-link"
           @click.prevent="navigate('{{ route('admin.languages.index') }}')">
          <i data-feather="globe" class="w-5 h-5"></i>
          <span>Idiomas</span>
        </a>
      </div>
      <div>
        <div class="sidebar-section">Geografía</div>
        <a href="{{ route('admin.countries.index') }}"
           class="sidebar-link"
           @click.prevent="navigate('{{ route('admin.countries.index') }}')">
          <i data-feather="flag" class="w-5 h-5"></i>
          <span>Países</span>
        </a>
        <a href="{{ route('admin.locations.index') }}"
           class="sidebar-link"
           @click.prevent="navigate('{{ route('admin.locations.index') }}')">
          <i data-feather="map-pin" class="w-5 h-5"></i>
          <span>Localizaciones</span>
        </a>
        <a href="{{ route('admin.addresses.index') }}"
           class="sidebar-link"
           @click.prevent="navigate('{{ route('admin.addresses.index') }}')">
          <i data-feather="navigation" class="w-5 h-5"></i>
          <span>Direcciones</span>
        </a>
      </div>
    </nav>

    <!-- BOTTOM: Versión y logout -->
    <div class="px-4 pb-4 mt-auto flex flex-col gap-2">
      <div class="flex items-center justify-between text-xs text-navy-200 dark:text-grayish-400 opacity-80 mb-1">
        <span>Versión</span>
        <span>1.3.0</span>
      </div>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button
          type="submit"
          class="flex items-center gap-2 w-full px-3 py-2 rounded-lg bg-navy-700 hover:bg-navy-600 dark:bg-grayish-700 dark:hover:bg-grayish-600 text-sm font-semibold transition">
          <i data-feather="log-out" class="w-5 h-5"></i>
          Cerrar sesión
        </button>
      </form>
    </div>
  </aside>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="flex-1 p-10 overflow-y-auto bg-navy-50 dark:bg-grayish-900 transition-colors duration-300 relative" id="main-content">
    <div x-show="loading" class="absolute inset-0 bg-white/80 dark:bg-grayish-900/70 flex items-center justify-center z-50" x-transition>
      <svg class="animate-spin h-8 w-8 text-navy-600 dark:text-grayish-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
    </div>
    <header class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-extrabold text-navy-800 dark:text-grayish-100">@yield('title', 'Panel de Administración')</h1>
      @auth
      <div class="flex items-center gap-3">
        <span class="font-semibold text-navy-800 dark:text-grayish-100">{{ Auth::user()->name }}</span>
        <img src="{{ asset('assets/avatar.png') }}" alt="Avatar" class="w-10 h-10 rounded-full border-2 border-navy-300 object-cover">
      </div>
      @endauth
    </header>
    <section id="panel" class="p-6 rounded-lg transition">
      @yield('content')
    </section>
  </main>
</div>

<script>
  function app() {
    return {
      darkMode: false,
      currentUrl: window.location.href,
      loading: false,
      init() {
        this.darkMode = localStorage.getItem('darkMode') === 'true';
        feather.replace();
      },
      toggleDark() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        feather.replace();
      },
      navigate(url) {
        this.loading = true;
        fetch(url)
          .then(res => res.text())
          .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const content = doc.querySelector('#panel')?.innerHTML;
            if (content) {
              const panel = document.querySelector('#panel');
              panel.innerHTML = content;
              feather.replace();
            }
            this.currentUrl = url;
            this.loading = false;
            window.history.pushState({}, '', url);
          })
          .catch((error) => {
            this.loading = false;
            console.error('Error al navegar:', error);
          });
      }
    }
  }
  window.addEventListener('popstate', () => location.reload());
</script>

@stack('scripts')
</body>
</html>
