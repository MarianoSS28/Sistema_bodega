<div style="position:relative;" wire:poll.30s="cargar">
    <button wire:click="toggleMostrar"
            style="position:relative; display:flex; align-items:center; gap:.3rem; color:rgba(255,255,255,.9); background:none; border:none; cursor:pointer; padding:.3rem; border-radius:var(--radius-md); transition:background var(--transition-fast);"
            onmouseover="this.style.background='rgba(255,255,255,.15)'"
            onmouseout="this.style.background='none'">
        <span style="font-size:1.15rem;">🔔</span>
        @if($totalAlertas > 0)
            <span style="position:absolute; top:-4px; right:-4px; background:var(--color-danger); color:#fff; font-size:.65rem; font-weight:700; border-radius:99px; min-width:18px; height:18px; display:flex; align-items:center; justify-content:center; border:2px solid var(--color-turquesa); animation: pulse-brand 2s infinite;">
                {{ $totalAlertas }}
            </span>
        @endif
    </button>

    @if($mostrar && $totalAlertas > 0)
    <div class="animate-scale-in"
         style="position:absolute; right:0; top:calc(100% + 10px); background:var(--color-surface); border-radius:var(--radius-lg); box-shadow:var(--shadow-lg); width:280px; z-index:200; border:1px solid var(--color-border); overflow:hidden;">

        <div style="padding:.75rem 1rem; border-bottom:1px solid var(--color-border); display:flex; justify-content:space-between; align-items:center; background:var(--color-turquesa-muted);">
            <span style="font-size:.85rem; font-weight:700; color:var(--color-text-primary);">Alertas de stock</span>
            <span class="badge badge-danger">{{ $agotados }} agotados</span>
        </div>

        <ul style="max-height:240px; overflow-y:auto; list-style:none; padding:0; margin:0;">
            @foreach($items as $item)
            <li style="padding:.6rem 1rem; border-bottom:1px solid var(--color-border); display:flex; justify-content:space-between; align-items:center; font-size:.83rem;">
                <span style="color:var(--color-text-primary); font-weight:500;">{{ $item->nombre }}</span>
                @if($item->tipo_alerta === 'AGOTADO')
                    <span class="badge badge-danger">AGOTADO</span>
                @else
                    <span class="badge badge-warning">{{ $item->stock }} uds.</span>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>