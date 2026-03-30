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
                @if($ultimoMetodoPago === 'efectivo' && $ultimoVuelto > 0)
                <div style="width:1px; background:var(--color-border);"></div>
                <div>
                    <p style="font-size:.75rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.2rem;">Vuelto</p>
                    <p style="font-size:1.6rem; font-weight:800; color:var(--color-celeste-dark);">S/ {{ number_format($ultimoVuelto, 2) }}</p>
                </div>
                @endif
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
        <button wire:click="abrirCobro">
    </div>
    @endif

    @if($mostrarModalCobro)
    <div class="modal-backdrop" style="z-index:999;">
        <div class="modal-box animate-scale-in" style="max-width:520px; width:100%;">
            <h2 class="modal-title">💳 Cobrar Venta — S/ {{ number_format($total, 2) }}</h2>

            {{-- Listado con check helada --}}
            <div style="margin-bottom:1.25rem; max-height:220px; overflow-y:auto;">
                <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.5rem;">¿Algún producto va helado? ❄️</p>
                @foreach($carrito as $i => $item)
                <label style="display:flex; align-items:center; gap:.75rem; padding:.45rem .6rem; border-radius:var(--radius-md); cursor:pointer; transition:background var(--transition-fast);"
                    onmouseover="this.style.background='var(--color-turquesa-muted)'" onmouseout="this.style.background=''">
                    <input type="checkbox" wire:model="heladasCarrito.{{ $i }}"
                        style="width:16px; height:16px; accent-color:var(--color-celeste-dark);">
                    <span style="flex:1; font-size:.875rem; font-weight:500;">{{ $item['nombre'] }}</span>
                    <span style="font-size:.8rem; color:var(--color-turquesa);">x{{ $item['cantidad'] }} — S/ {{ number_format($item['subtotal'], 2) }}</span>
                    @if($heladasCarrito[$i] ?? false)
                        <span style="font-size:.85rem;">❄️</span>
                    @endif
                </label>
                @endforeach
            </div>

            {{-- Método de pago --}}
            <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.5rem;">Método de pago</p>
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:.5rem; margin-bottom:1rem;">
                @foreach(['efectivo' => '💵 Efectivo', 'yape' => '📱 Yape', 'plin' => '💜 Plin', 'otro' => '💳 Otro'] as $val => $label)
                <button wire:click="$set('metodoPago', '{{ $val }}')"
                        style="padding:.55rem .4rem; border-radius:var(--radius-md); font-size:.8rem; font-weight:600; cursor:pointer; border:2px solid {{ $metodoPago === $val ? 'var(--color-turquesa)' : 'var(--color-border)' }}; background:{{ $metodoPago === $val ? 'var(--color-turquesa-muted)' : 'var(--color-surface)' }}; color:{{ $metodoPago === $val ? 'var(--color-turquesa-dark)' : 'var(--color-text-secondary)' }}; transition:all var(--transition-fast);">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- QR si es Yape o Plin --}}
            @if(in_array($metodoPago, ['yape','plin']))
            @php $comercio = \Illuminate\Support\Facades\DB::table('bodega.comercio')->where('estado',1)->first(); @endphp
            @if($comercio)
            <div style="text-align:center; margin-bottom:1rem;">
                @if($metodoPago === 'yape' && $comercio->yape_qr)
                    <img src="{{ Storage::url($comercio->yape_qr) }}" style="max-height:160px; border-radius:var(--radius-md); border:2px solid var(--color-turquesa);">
                    <p style="font-size:.78rem; color:var(--color-text-muted); margin-top:.4rem;">Escanea el QR de Yape</p>
                @elseif($metodoPago === 'plin' && $comercio->plin_qr)
                    <img src="{{ Storage::url($comercio->plin_qr) }}" style="max-height:160px; border-radius:var(--radius-md); border:2px solid #7c3aed;">
                    <p style="font-size:.78rem; color:var(--color-text-muted); margin-top:.4rem;">Escanea el QR de Plin</p>
                @endif
            </div>
            @endif
            @endif

            {{-- Efectivo recibido + vuelto --}}
            @if($metodoPago === 'efectivo')
            <div style="display:flex; gap:.75rem; align-items:flex-end; margin-bottom:1rem;">
                <div style="flex:1;">
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Efectivo recibido (S/)</label>
                    <input wire:model.live="efectivoRecibido" type="number" step="0.10" min="{{ $total }}" class="input" placeholder="0.00" style="font-size:1.1rem; font-weight:700;">
                </div>
                <div style="flex:1; background:var(--color-turquesa-muted); border-radius:var(--radius-md); padding:.75rem; text-align:center;">
                    <p style="font-size:.75rem; font-weight:600; color:var(--color-text-muted); margin-bottom:.2rem;">VUELTO</p>
                    <p style="font-size:1.6rem; font-weight:800; color:{{ $this->calcularVuelto() >= 0 ? 'var(--color-turquesa)' : 'var(--color-danger)' }};">
                        S/ {{ number_format($this->calcularVuelto(), 2) }}
                    </p>
                </div>
            </div>
            @if($efectivoRecibido !== '' && (float)$efectivoRecibido < $total)
                <div class="alert alert-danger" style="margin-bottom:.75rem;">⚠️ El efectivo no cubre el total.</div>
            @endif
            @endif

            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:.5rem;">
                <button wire:click="$set('mostrarModalCobro', false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="registrarVenta"
                        @if($metodoPago === 'efectivo' && ($efectivoRecibido === '' || (float)$efectivoRecibido < $total)) disabled @endif
                        class="btn btn-success" style="opacity: {{ $metodoPago === 'efectivo' && ($efectivoRecibido === '' || (float)$efectivoRecibido < $total) ? '.5' : '1' }};">
                    ✅ Confirmar Venta
                </button>
            </div>
        </div>
    </div>
    @endif
</div>