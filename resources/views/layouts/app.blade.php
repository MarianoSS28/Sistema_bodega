<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Bodega</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>

    <nav class="nav-bar">
        <span class="brand">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Bodega
        </span>

        <a href="{{ route('dashboard') }}"  class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('ventas') }}"     class="nav-link {{ request()->routeIs('ventas')    ? 'active' : '' }}">Ventas</a>
        <a href="{{ route('productos') }}"  class="nav-link {{ request()->routeIs('productos') ? 'active' : '' }}">Productos</a>
        <a href="{{ route('historial') }}"  class="nav-link {{ request()->routeIs('historial') ? 'active' : '' }}">Historial</a>
        <a href="{{ route('vouchers') }}"   class="nav-link {{ request()->routeIs('vouchers')  ? 'active' : '' }}">Vouchers</a>

        <div style="margin-left:auto">
            <livewire:stock-alertas-badge />
        </div>
    </nav>

    <main style="padding: 1.5rem; max-width: 1400px; margin: 0 auto;">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>