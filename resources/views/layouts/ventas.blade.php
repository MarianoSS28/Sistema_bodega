<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ventas — Sistema Bodega</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>

    {{-- Barra mínima: solo nombre de usuario y botón volver --}}
    <div style="
        height: 48px;
        background: var(--gradient-brand);
        display: flex;
        align-items: center;
        padding: 0 1.25rem;
        gap: 1rem;
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 0;
        z-index: 100;
    ">
        <a href="{{ route('dashboard') }}"
           style="color:rgba(255,255,255,.85); font-size:.82rem; font-weight:600;
                  text-decoration:none; display:flex; align-items:center; gap:.35rem;
                  padding:.3rem .7rem; border-radius:var(--radius-pill);
                  transition:background var(--transition-fast);"
           onmouseover="this.style.background='rgba(255,255,255,.2)'"
           onmouseout="this.style.background='transparent'">
            &larr; Panel
        </a>

        <span style="color:rgba(255,255,255,.4); font-size:.9rem;">|</span>

        <span style="color:#fff; font-weight:700; font-size:.9rem; letter-spacing:-.3px;">
            Punto de Venta
        </span>

        <div style="margin-left:auto; display:flex; align-items:center; gap:.75rem;">
            <span style="color:rgba(255,255,255,.75); font-size:.82rem;">
                {{ Auth::user()->nombre_completo }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        style="color:rgba(255,255,255,.75); background:rgba(255,255,255,.12);
                               border:none; border-radius:var(--radius-pill);
                               padding:.3rem .75rem; font-size:.78rem; font-weight:600;
                               cursor:pointer; transition:all var(--transition-fast);"
                        onmouseover="this.style.background='rgba(255,255,255,.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,.12)'">
                    Salir
                </button>
            </form>
        </div>
    </div>

    <main style="padding: 1.25rem; max-width: 900px; margin: 0 auto;">
        {{ $slot }}
    </main>

    @stack('scripts')
    @livewireScripts
</body>
</html>