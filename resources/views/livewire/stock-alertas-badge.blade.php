<div style="position:relative;" wire:poll.10s="cargar">
    <button wire:click="toggleMostrar"
            style="
                position: relative;
                display: flex; align-items: center; gap: .4rem;
                color: rgba(255,255,255,.95);
                background: none; border: none;
                cursor: pointer;
                padding: .35rem .6rem;
                border-radius: var(--radius-md);
                transition: background var(--transition-fast);
                font-size: .82rem; font-weight: 600;
            "
            onmouseover="this.style.background='rgba(255,255,255,.18)'"
            onmouseout="this.style.background='none'">

        {{-- Icono campana con glow --}}
        <span style="
            position: relative;
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: var(--radius-md);
            background: {{ $totalAlertas > 0 ? 'rgba(255,255,255,.15)' : 'rgba(255,255,255,.08)' }};
            font-size: 1rem;
            transition: all var(--transition-base);
        ">🔔
            @if($totalAlertas > 0)
            {{-- Badge de contador --}}
            <span style="
                position: absolute; top: -5px; right: -5px;
                background: var(--color-danger);
                color: #fff;
                font-size: .62rem; font-weight: 800;
                border-radius: 99px;
                min-width: 18px; height: 18px;
                display: flex; align-items: center; justify-content: center;
                border: 2px solid rgba(39,184,109,.8);
                animation: pulse-brand 2s infinite;
                letter-spacing: -.3px;
            ">{{ $totalAlertas }}</span>
            @endif
        </span>

        @if($totalAlertas > 0)
        <span style="display: flex; flex-direction: column; line-height: 1.2; text-align: left;">
            <span style="font-size: .7rem; opacity: .8;">Alertas</span>
            <span style="font-size: .85rem; font-weight: 700;">{{ $totalAlertas }}</span>
        </span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if($mostrar)
    <div class="animate-scale-in"
         style="
            position: absolute; right: 0; top: calc(100% + 10px);
            background: var(--color-surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            width: 300px; z-index: 200;
            border: 1px solid var(--color-border);
            overflow: hidden;
         ">

        {{-- Header del dropdown --}}
        <div style="
            padding: .85rem 1rem;
            background: var(--gradient-brand);
            display: flex; justify-content: space-between; align-items: center;
        ">
            <div style="display: flex; align-items: center; gap: .5rem;">
                <span style="font-size: 1rem;">🔔</span>
                <span style="font-size: .9rem; font-weight: 700; color: #fff;">Alertas de Stock</span>
            </div>
            <div style="display: flex; gap: .4rem;">
                @if($agotados > 0)
                <span style="
                    background: rgba(255,255,255,.2);
                    color: #fff;
                    font-size: .7rem; font-weight: 700;
                    padding: .2rem .55rem;
                    border-radius: var(--radius-pill);
                    border: 1px solid rgba(255,255,255,.3);
                ">🚫 {{ $agotados }} agotados</span>
                @endif
                @if($porAcabar > 0)
                <span style="
                    background: rgba(245,158,11,.25);
                    color: #fde68a;
                    font-size: .7rem; font-weight: 700;
                    padding: .2rem .55rem;
                    border-radius: var(--radius-pill);
                    border: 1px solid rgba(245,158,11,.4);
                ">⚠️ {{ $porAcabar }}</span>
                @endif
            </div>
        </div>

        @if($totalAlertas === 0)
        <div style="padding: 2rem; text-align: center; color: var(--color-text-muted);">
            <div style="font-size: 1.75rem; margin-bottom: .5rem;">✅</div>
            <p style="font-size: .85rem; font-weight: 600;">Todo en orden</p>
        </div>
        @else
        {{-- Resumen numérico --}}
        <div style="
            padding: .65rem 1rem;
            background: var(--color-surface-2);
            border-bottom: 1px solid var(--color-border);
            display: flex; gap: 1rem;
        ">
            <div style="display: flex; align-items: center; gap: .35rem;">
                <span style="width: 8px; height: 8px; background: var(--color-danger); border-radius: 50%; display: inline-block;"></span>
                <span style="font-size: .78rem; color: var(--color-danger); font-weight: 600;">{{ $agotados }} sin stock</span>
            </div>
            <div style="display: flex; align-items: center; gap: .35rem;">
                <span style="width: 8px; height: 8px; background: var(--color-warning); border-radius: 50%; display: inline-block;"></span>
                <span style="font-size: .78rem; color: var(--color-warning); font-weight: 600;">{{ $porAcabar }} por acabar</span>
            </div>
        </div>

        <ul style="max-height: 260px; overflow-y: auto; list-style: none; padding: 0; margin: 0;">
            @foreach($items as $item)
            <li style="
                padding: .6rem 1rem;
                border-bottom: 1px solid var(--color-border);
                display: flex; justify-content: space-between; align-items: center;
                font-size: .83rem;
                transition: background var(--transition-fast);
            "
            onmouseover="this.style.background='var(--color-turquesa-muted)'"
            onmouseout="this.style.background=''">
                <div style="display: flex; align-items: center; gap: .5rem;">
                    @if($item->tipo_alerta === 'AGOTADO')
                        <span style="width: 6px; height: 6px; background: var(--color-danger); border-radius: 50%; flex-shrink: 0;"></span>
                    @else
                        <span style="width: 6px; height: 6px; background: var(--color-warning); border-radius: 50%; flex-shrink: 0;"></span>
                    @endif
                    <span style="color: var(--color-text-primary); font-weight: 500; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $item->nombre }}</span>
                </div>
                @if($item->tipo_alerta === 'AGOTADO')
                    <span style="
                        background: var(--color-danger-light);
                        color: var(--color-danger);
                        font-size: .7rem; font-weight: 700;
                        padding: .2rem .6rem;
                        border-radius: var(--radius-pill);
                        border: 1px solid rgba(224,81,58,.2);
                    ">AGOTADO</span>
                @else
                    <span style="
                        background: var(--color-warning-light);
                        color: var(--color-warning);
                        font-size: .7rem; font-weight: 700;
                        padding: .2rem .6rem;
                        border-radius: var(--radius-pill);
                        border: 1px solid rgba(245,158,11,.25);
                    ">{{ $item->stock }} uds.</span>
                @endif
            </li>
            @endforeach
        </ul>
        @endif

        {{-- Footer --}}
        <div style="padding: .6rem 1rem; background: var(--color-surface-2); border-top: 1px solid var(--color-border); text-align: center;">
            <a href="/productos" style="font-size: .78rem; color: var(--color-celeste-dark); font-weight: 600; text-decoration: none;">
                Ver todos los productos →
            </a>
        </div>
    </div>
    @endif
</div>