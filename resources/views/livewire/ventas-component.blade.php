<div class="animate-fade-in">
    <h1 class="page-title">Nueva Venta</h1>

    {{-- ✅ Modal de venta exitosa --}}
    @if($ventaExitosa)
    <div class="modal-backdrop" style="z-index:999;">
        <div class="animate-scale-in" style="
            background: var(--color-surface);
            border-radius: var(--radius-xl);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow-lg);
            text-align: center;
            border: 2px solid var(--color-turquesa);
            position: relative;
            overflow: hidden;
        ">
            {{-- Fondo decorativo --}}
            <div style="
                position: absolute; inset: 0;
                background: radial-gradient(ellipse at 50% 0%, rgba(39,184,109,.12) 0%, transparent 70%);
                pointer-events: none;
            "></div>

            {{-- Ícono animado --}}
            <div style="
                width: 80px; height: 80px;
                background: var(--gradient-brand);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-size: 2.2rem;
                margin: 0 auto 1.25rem;
                box-shadow: 0 0 0 12px rgba(39,184,109,.12), 0 0 0 24px rgba(39,184,109,.06);
                animation: pulse-brand 2s infinite;
            ">✅</div>

            <p style="font-size: 1.5rem; font-weight: 800; color: var(--color-text-primary); letter-spacing: -.5px; margin-bottom: .4rem;">
                ¡Venta Registrada!
            </p>
            <p style="font-size: .9rem; color: var(--color-text-secondary); margin-bottom: 1.5rem;">
                La transacción se completó correctamente
            </p>

            <div style="
                background: var(--color-turquesa-muted);
                border: 1.5px solid var(--color-turquesa-light);
                border-radius: var(--radius-lg);
                padding: 1rem 1.5rem;
                margin-bottom: 1.75rem;
                display: flex;
                justify-content: space-around;
            ">
                <div>
                    <p style="font-size: .75rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: .2rem;">Productos</p>
                    <p style="font-size: 1.6rem; font-weight: 800; color: var(--color-text-primary);">{{ $ultimasCantItems }}</p>
                </div>
                <div style="width: 1px; background: var(--color-border);"></div>
                <div>
                    <p style="font-size: .75rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: .2rem;">Total</p>
                    <p style="font-size: 1.6rem; font-weight: 800; color: var(--color-turquesa);">S/ {{ number_format($ultimoTotal, 2) }}</p>
                </div>
            </div>

            <button wire:click="cerrarExito" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: .95rem; padding: .75rem;">
                🛒 Nueva Venta
            </button>
        </div>
    </div>
    @endif

    {{-- Buscador — auto-search sin botón --}}
    <div class="card" style="padding:1.25rem; margin-bottom:1.25rem;">
        <p style="font-size:.8rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.6rem;">
            🔍 Escanear / Escribir código
        </p>
        <div style="position: relative;">
            <input
                wire:model.live.debounce.400ms="codigoBusqueda"
                placeholder="Código de barras — se busca automáticamente..."
                class="input"
                style="padding-right: 2.5rem;"
                autofocus>
            {{-- Indicador de carga --}}
            <div wire:loading wire:target="updatedCodigoBusqueda"
                 style="position:absolute; right:.75rem; top:50%; transform:translateY(-50%); width:18px; height:18px; border:2px solid var(--color-turquesa); border-top-color:transparent; border-radius:50%; animation: spin-slow .7s linear infinite;">
            </div>
        </div>
        @if($error)
            <div class="alert alert-danger" style="margin-top:.75rem;">{{ $error }}</div>
        @endif
        <p style="font-size:.75rem; color:var(--color-text-muted); margin-top:.5rem;">
            💡 Escanea el código de barras o escríbelo — se agrega solo al carrito
        </p>
    </div>

    {{-- Foto último producto --}}
    @if(count($carrito) && $carrito[array_key_last($carrito)]['foto_path'])
        <div style="display:flex; justify-content:center; margin-bottom:1.25rem;">
            <img src="{{ Storage::url($carrito[array_key_last($carrito)]['foto_path']) }}"
                 alt="Producto"
                 class="animate-scale-in"
                 style="height:140px; width:140px; object-fit:cover; border-radius:var(--radius-xl); box-shadow:var(--shadow-lg); border:3px solid var(--color-turquesa);">
        </div>
    @endif

    {{-- Carrito --}}
    <div class="table-wrap animate-fade-in delay-100" style="margin-bottom:1.25rem;">
        <div style="padding:.85rem 1.25rem; border-bottom:1px solid var(--color-border);">
            <span style="font-weight:700; font-size:.95rem;">🛒 Carrito</span>
            @if(count($carrito))
                <span class="badge badge-info" style="margin-left:.5rem;">{{ count($carrito) }} ítems</span>
            @endif
        </div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Cant.</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-center">—</th>
                </tr>
            </thead>
            <tbody>
                @forelse($carrito as $i => $item)
                <tr class="animate-fade-in">
                    <td>
                        <div style="display:flex; align-items:center; gap:.6rem;">
                            @if($item['foto_path'])
                                <img src="{{ Storage::url($item['foto_path']) }}"
                                     style="width:36px; height:36px; object-fit:cover; border-radius:var(--radius-sm);">
                            @endif
                            <span style="font-weight:500;">{{ $item['nombre'] }}</span>
                        </div>
                    </td>
                    <td class="text-right">S/ {{ number_format($item['precio_unitario'], 2) }}</td>
                    <td class="text-right">
                        <span class="badge badge-info">{{ $item['cantidad'] }}</span>
                    </td>
                    <td class="text-right" style="font-weight:700;">S/ {{ number_format($item['subtotal'], 2) }}</td>
                    <td class="text-center">
                        <button wire:click="quitarItem({{ $i }})" class="link-action danger">✕</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
                        <div style="font-size:2rem; margin-bottom:.5rem;">🛒</div>
                        Carrito vacío — escanea un producto para comenzar
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(count($carrito))
            <tfoot>
                <tr style="background:var(--color-turquesa-muted); border-top:2px solid var(--color-border);">
                    <td colspan="3" style="text-align:right; font-weight:700; padding:.85rem 1rem;">TOTAL</td>
                    <td style="text-align:right; font-size:1.15rem; font-weight:800; color:var(--color-turquesa); padding:.85rem 1rem;">S/ {{ number_format($total, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if(count($carrito))
    <div style="display:flex; justify-content:flex-end;">
        <button wire:click="registrarVenta"
                wire:confirm="¿Confirmar venta por S/ {{ number_format($total, 2) }}?"
                class="btn btn-success"
                style="font-size:1rem; padding:.7rem 2rem; animation: pulse-brand 2s infinite;">
            ✅ Registrar Venta — S/ {{ number_format($total, 2) }}
        </button>
    </div>
    @endif
</div>