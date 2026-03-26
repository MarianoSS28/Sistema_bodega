<div class="animate-fade-in">
    <h1 class="page-title">Nueva Venta</h1>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">✅ {{ session('ok') }}</div>
    @endif

    {{-- Buscador --}}
    <div class="card" style="padding:1.25rem; margin-bottom:1.25rem;">
        <p style="font-size:.8rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.6rem;">Escanear producto</p>
        <div style="display:flex; gap:.65rem;">
            <input
                wire:model="codigoBusqueda"
                wire:keydown.enter="buscarProducto"
                placeholder="Código de barras..."
                class="input"
                autofocus>
            <button wire:click="buscarProducto" class="btn btn-primary">
                🔍 Buscar
            </button>
        </div>
        @if($error)
            <div class="alert alert-danger" style="margin-top:.75rem;">{{ $error }}</div>
        @endif
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
                <tr>
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
                        Carrito vacío
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
            ✅ Registrar Venta
        </button>
    </div>
    @endif
</div>