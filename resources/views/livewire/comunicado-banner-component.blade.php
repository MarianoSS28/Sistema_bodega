<div>
    @if($mostrar && $comunicado)
    <div class="modal-backdrop animate-fade-in" style="z-index:9999;">
        <div class="animate-scale-in" style="
            background:#fff; border-radius:24px; padding:2rem;
            width:100%; max-width:600px;
            box-shadow:0 8px 32px rgba(15,45,30,.18);
            max-height:85vh; display:flex; flex-direction:column;
        ">
            <div style="text-align:center; margin-bottom:1.25rem;">
                <div style="font-size:2rem; margin-bottom:.5rem;">📢</div>
                <h2 style="font-size:1.2rem; font-weight:800; color:#0f2d1e;">{{ $comunicado->titulo }}</h2>
                <p style="font-size:.75rem; color:#89b09e; margin-top:.25rem;">
                    {{ \Carbon\Carbon::parse($comunicado->fecha_creacion)->format('d/m/Y') }}
                </p>
            </div>
            <div style="flex:1; overflow-y:auto; border:1.5px solid #d1ede2; border-radius:12px;
                        padding:1rem 1.25rem; font-size:.875rem; line-height:1.8; color:#4a7360;
                        white-space:pre-wrap; margin-bottom:1.5rem; background:#f4fdf8;">
                {{ $comunicado->contenido }}
            </div>
            <div style="display:flex; justify-content:flex-end;">
                <button wire:click="aceptar"
                        style="padding:.6rem 2rem; border-radius:9999px;
                            background:linear-gradient(135deg,#27B86D,#43AA72);
                            color:#fff; border:none; font-weight:700; font-size:.875rem; cursor:pointer;">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>