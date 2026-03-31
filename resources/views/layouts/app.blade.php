<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Bodega</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $comercioActual = auth()->check()
            ? \App\Models\Comercio::find(auth()->user()->id_comercio)
            : null;
        $colorPrimario = $comercioActual?->color_primario ?? '#27B86D';
    @endphp
    <style>
    :root {
        --color-turquesa: {{ $colorPrimario }};
        --color-turquesa-dark: color-mix(in srgb, {{ $colorPrimario }} 80%, black);
        --color-turquesa-light: color-mix(in srgb, {{ $colorPrimario }} 20%, white);
        --color-turquesa-muted: color-mix(in srgb, {{ $colorPrimario }} 10%, white);
    }
    </style>
    @livewireStyles
</head>
<body>

    <nav class="nav-bar">
        <span class="brand">
            @if($comercioActual?->logo_path)
                <img src="{{ Storage::url($comercioActual->logo_path) }}"
                    style="width:28px; height:28px; object-fit:cover; border-radius:6px; border:2px solid rgba(255,255,255,.3);">
            @else
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            @endif
            {{ $comercioActual?->nombre ?? 'Bodega' }}
        </span>

        <div style="display:flex; align-items:center; gap:.15rem; flex:1; overflow:hidden;">
            @foreach(Auth::user()->menus()->orderBy('bodega.menus.id')->get() as $menu)
            <a href="{{ route($menu->ruta) }}"
            class="nav-link {{ request()->routeIs($menu->ruta) ? 'active' : '' }}"
            style="white-space:nowrap; display:inline-flex; align-items:center; gap:.35rem;">
                {!! \App\Helpers\IconoHelper::get($menu->icono ?? $menu->ruta) !!}
                <span style="font-size:.78rem;">{{ $menu->nombre }}</span>
            </a>
            @endforeach
        </div>

        <div style="margin-left:auto;">
            <livewire:stock-alertas-badge />
        </div>

        <form method="POST" action="{{ route('logout') }}" style="margin-left:.5rem;">
            @csrf
            <button type="submit" class="nav-link"
                    style="background:rgba(255,255,255,.15); cursor:pointer; border:none;">
                {{ Auth::user()->nombre_completo }} &nbsp;·&nbsp; Salir
            </button>
        </form>
    </nav>

    <main style="padding: 1.5rem; max-width: 1400px; margin: 0 auto;">
        {{ $slot }}
    </main>

    @stack('scripts')
    @livewireScripts
</body>
</html>