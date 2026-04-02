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
    .sidebar {
        width: 220px;
        min-height: 100vh;
        background: var(--gradient-brand);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0;
        z-index: 100;
        box-shadow: var(--shadow-md);
    }
    .sidebar-brand {
        padding: 1.25rem 1rem 1rem;
        display: flex;
        align-items: center;
        gap: .6rem;
        border-bottom: 1px solid rgba(255,255,255,.15);
        margin-bottom: .5rem;
    }
    .sidebar-brand span {
        font-weight: 800;
        font-size: 1rem;
        color: #fff;
        letter-spacing: -.3px;
        line-height: 1.2;
    }
    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        padding: .25rem .5rem;
    }
    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 99px; }
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .55rem .75rem;
        border-radius: var(--radius-md);
        color: rgba(255,255,255,.82);
        font-size: .83rem;
        font-weight: 500;
        text-decoration: none;
        transition: background var(--transition-fast), color var(--transition-fast);
        margin-bottom: .1rem;
        white-space: nowrap;
    }
    .sidebar-link:hover, .sidebar-link.active {
        background: rgba(255,255,255,.2);
        color: #fff;
    }
    .sidebar-footer {
        padding: .75rem .5rem;
        border-top: 1px solid rgba(255,255,255,.15);
    }
    .main-content {
        margin-left: 220px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .topbar {
        height: 52px;
        background: var(--color-surface);
        border-bottom: 1px solid var(--color-border);
        display: flex;
        align-items: center;
        padding: 0 1.5rem;
        gap: 1rem;
        position: sticky;
        top: 0;
        z-index: 50;
    }
    </style>
    @livewireStyles
</head>
<body style="background:var(--color-bg);">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            @if($comercioActual?->logo_path)
                <img src="{{ Storage::url($comercioActual->logo_path) }}"
                    style="width:32px; height:32px; object-fit:cover; border-radius:8px; border:2px solid rgba(255,255,255,.3); flex-shrink:0;">
            @else
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            @endif
            <span>{{ $comercioActual?->nombre ?? 'Bodega' }}</span>
        </div>

        <nav class="sidebar-nav">
            @foreach(Auth::user()->menus()->orderBy('bodega.menus.id')->get() as $menu)
            <a href="{{ route($menu->ruta) }}"
               class="sidebar-link {{ request()->routeIs($menu->ruta) ? 'active' : '' }}">
                {!! \App\Helpers\IconoHelper::get($menu->icono ?? $menu->ruta) !!}
                {{ $menu->nombre }}
            </a>
            @endforeach
        </nav>

        <div class="sidebar-footer">
            <div style="padding:.4rem .75rem; margin-bottom:.4rem;">
                <p style="font-size:.72rem; color:rgba(255,255,255,.55); font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Usuario</p>
                <p style="font-size:.82rem; color:rgba(255,255,255,.9); font-weight:600; margin-top:.1rem;">{{ Auth::user()->nombre_completo }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link" style="width:100%; border:none; cursor:pointer; background:rgba(255,255,255,.12);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="main-content">
        {{-- Topbar --}}
        <div class="topbar">
            <span style="font-size:.85rem; font-weight:700; color:var(--color-text-secondary);">
                {{ collect(Auth::user()->menus()->get())->firstWhere('ruta', request()->route()->getName())?->nombre ?? 'Dashboard' }}
            </span>
            <div style="margin-left:auto;">
                <livewire:stock-alertas-badge/>
            </div>
        </div>

        <main style="padding: 1.5rem; flex:1;">
            <livewire:comunicado-banner-component/>
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>