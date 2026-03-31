<div style="max-width:680px; width:100%; background:#fff; border-radius:24px; padding:2rem; box-shadow:0 8px 32px rgba(15,45,30,.18);">
    <h1 style="font-size:1.3rem; font-weight:800; color:#0f2d1e; margin-bottom:.25rem;">
        Términos y Condiciones
    </h1>
    @if($termino)
        <p style="font-size:.78rem; color:#89b09e; margin-bottom:1rem;">
            v{{ $termino->version }} — {{ \Carbon\Carbon::parse($termino->fecha_creacion)->format('d/m/Y') }}
        </p>
        <div style="max-height:340px; overflow-y:auto; border:1.5px solid #d1ede2; border-radius:12px;
                    padding:1rem 1.25rem; font-size:.875rem; line-height:1.8; color:#4a7360;
                    white-space:pre-wrap; margin-bottom:1.5rem; background:#f4fdf8;">
            {{ $termino->contenido }}
        </div>
    @else
        <p style="color:#89b09e; margin-bottom:1.5rem;">No hay términos publicados.</p>
    @endif

    <p style="font-size:.82rem; color:#4a7360; margin-bottom:1.25rem; font-weight:500;">
        Para continuar usando el sistema debes aceptar los términos y condiciones.
    </p>

    <div style="display:flex; gap:.75rem; justify-content:flex-end;">
        <button wire:click="rechazar"
                style="padding:.55rem 1.4rem; border-radius:9999px; border:1.5px solid #e0513a;
                       color:#e0513a; background:#fff; font-weight:600; font-size:.875rem; cursor:pointer;">
            Rechazar y salir
        </button>
        <button wire:click="aceptar"
                style="padding:.55rem 1.4rem; border-radius:9999px; background:linear-gradient(135deg,#27B86D,#43AA72);
                       color:#fff; border:none; font-weight:700; font-size:.875rem; cursor:pointer;">
            Acepto los términos
        </button>
    </div>
</div>