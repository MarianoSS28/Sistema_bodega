<div class="animate-fade-in" x-data="escaner(@this)">

    {{-- Captura global de teclado (invisible) --}}
    <input
        id="input-escaner-oculto"
        type="text"
        x-ref="inputOculto"
        style="position:fixed; top:-200px; left:-200px; opacity:0; width:1px; height:1px;"
        autocomplete="off"
        tabindex="0"
    >

    <h1 class="page-title">Nueva Venta</h1>

    {{-- ✅ Modal venta exitosa --}}
    @if($ventaExitosa)
    @teleport('body')
    <div class="modal-backdrop" style="z-index:999;">
        <div class="animate-scale-in" style="
            background: var(--color-surface); border-radius: var(--radius-xl);
            padding: 2.5rem 2rem; width: 100%; max-width: 420px;
            box-shadow: var(--shadow-lg); text-align: center;
            border: 2px solid var(--color-turquesa); position: relative; overflow: hidden;
        ">
            <div style="position:absolute; inset:0;
                        background:radial-gradient(ellipse at 50% 0%,rgba(39,184,109,.12) 0%,transparent 70%);
                        pointer-events:none;"></div>

            <div style="width:80px; height:80px; background:var(--gradient-brand); border-radius:50%;
                        display:flex; align-items:center; justify-content:center; font-size:2.2rem;
                        margin:0 auto 1.25rem;
                        box-shadow:0 0 0 12px rgba(39,184,109,.12),0 0 0 24px rgba(39,184,109,.06);
                        animation:pulse-brand 2s infinite;">&#10003;</div>

            <p style="font-size:1.5rem; font-weight:800; color:var(--color-text-primary); margin-bottom:.4rem;">
                Venta Registrada
            </p>
            <p style="font-size:.9rem; color:var(--color-text-secondary); margin-bottom:1.5rem;">
                La transacción se completó correctamente
            </p>

            <div style="background:var(--color-turquesa-muted); border:1.5px solid var(--color-turquesa-light);
                        border-radius:var(--radius-lg); padding:1rem 1.5rem; margin-bottom:1.75rem;
                        display:flex; justify-content:space-around;">
                <div>
                    <p style="font-size:.75rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.2rem;">Productos</p>
                    <p style="font-size:1.6rem; font-weight:800; color:var(--color-text-primary);">{{ $ultimasCantItems }}</p>
                </div>
                <div style="width:1px; background:var(--color-border);"></div>
                <div>
                    <p style="font-size:.75rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.2rem;">Total</p>
                    <p style="font-size:1.6rem; font-weight:800; color:var(--color-turquesa);">S/ {{ number_format($ultimoTotal, 2) }}</p>
                </div>
                @if($ultimoMetodoPago === 'efectivo' && $ultimoVuelto > 0)
                <div style="width:1px; background:var(--color-border);"></div>
                <div>
                    <p style="font-size:.75rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.2rem;">Vuelto</p>
                    <p style="font-size:1.6rem; font-weight:800; color:var(--color-celeste-dark);">S/ {{ number_format($ultimoVuelto, 2) }}</p>
                </div>
                @endif
            </div>

            <button wire:click="cerrarExito" class="btn btn-primary"
                    style="width:100%; justify-content:center; font-size:.95rem; padding:.75rem;">
                Nueva Venta
            </button>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Zona de escaneo --}}
    <div class="card" style="padding:1.25rem; margin-bottom:1.25rem;">
        <p style="font-size:.8rem; font-weight:600; color:var(--color-text-muted);
                  text-transform:uppercase; letter-spacing:.06em; margin-bottom:.75rem;">
            Escanear código de barras
        </p>

        <div x-show="activo"
             style="display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem;
                    background:var(--color-turquesa-muted); border-radius:var(--radius-md);
                    border:1.5px solid var(--color-turquesa); margin-bottom:.75rem;">
            <div style="width:10px; height:10px; background:var(--color-turquesa); border-radius:50%;
                        animation:pulse-brand 1.5s infinite;"></div>
            <span style="font-size:.85rem; font-weight:600; color:var(--color-turquesa-dark);">
                Listo para escanear — escribe o usa el lector
            </span>
            <span x-show="buffer.length > 0"
                  style="margin-left:auto; font-family:monospace; font-size:.82rem;
                         color:var(--color-text-muted);"
                  x-text="buffer"></span>
        </div>

        <div x-show="!activo"
             style="display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem;
                    background:var(--color-warning-light); border-radius:var(--radius-md);
                    border:1.5px solid var(--color-warning); margin-bottom:.75rem; cursor:pointer;"
             @click="activar">
            <div style="width:10px; height:10px; background:var(--color-warning); border-radius:50%;"></div>
            <span style="font-size:.85rem; font-weight:600; color:var(--color-warning);">
                Haz clic aquí para activar el escaneo
            </span>
        </div>

        @if($error)
        <div class="alert alert-danger" style="margin-bottom:.75rem;">{{ $error }}</div>
        @endif

        <div style="display:flex; align-items:center; gap:.75rem;">
            <button wire:click="toggleManual"
                    class="btn btn-secondary"
                    style="font-size:.8rem; padding:.4rem .9rem;">
                {{ $mostrarManual ? 'Cancelar' : 'Ingresar código manual' }}
            </button>
            <span style="font-size:.75rem; color:var(--color-text-muted);">
                Usa esto si el lector no funciona
            </span>
        </div>

        @if($mostrarManual)
        <div style="margin-top:.75rem; display:flex; gap:.65rem;" class="animate-fade-in">
            <input wire:model="codigoManual"
                   wire:keydown.enter="buscarManual"
                   class="input"
                   placeholder="Escribe el código de barras..."
                   style="flex:1;"
                   autofocus>
            <button wire:click="buscarManual" class="btn btn-primary">Buscar</button>
        </div>
        @endif
    </div>

    {{-- Foto último producto agregado --}}
    @if(count($carrito) && $carrito[array_key_last($carrito)]['foto_path'])
    <div style="display:flex; justify-content:center; margin-bottom:1.25rem;">
        <img src="{{ Storage::url($carrito[array_key_last($carrito)]['foto_path']) }}"
             class="animate-scale-in"
             style="height:130px; width:130px; object-fit:cover; border-radius:var(--radius-xl);
                    box-shadow:var(--shadow-lg); border:3px solid var(--color-turquesa);">
    </div>
    @endif

    {{-- Carrito --}}
    <div class="table-wrap animate-fade-in delay-100" style="margin-bottom:1.25rem;">
        <div style="padding:.85rem 1.25rem; border-bottom:1px solid var(--color-border); display:flex; align-items:center; gap:.75rem;">
            <span style="font-weight:700; font-size:.95rem;">Carrito</span>
            @if(count($carrito))
            <span class="badge badge-info">{{ count($carrito) }} ítems</span>
            @endif
            @if($precioHelada > 0)
            <span style="margin-left:auto; font-size:.75rem; color:var(--color-celeste-dark);
                         background:var(--color-celeste-muted); padding:.2rem .65rem;
                         border-radius:var(--radius-pill); border:1px solid var(--color-celeste-dark);">
                🧊 Precio helada: +S/ {{ number_format($precioHelada, 2) }}
            </span>
            @endif
        </div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Precio base</th>
                    <th class="text-center">🧊</th>
                    <th class="text-right">Precio real</th>
                    <th class="text-right">Cant.</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-center">—</th>
                </tr>
            </thead>
            <tbody>
                @forelse($carrito as $i => $item)
                @php
                    $esHelada    = $heladasCarrito[$i] ?? false;
                    $precioReal  = $item['precio_unitario'] + ($esHelada ? $precioHelada : 0);
                    $subtotalReal = $precioReal * $item['cantidad'];
                @endphp
                <tr class="animate-fade-in" style="{{ $esHelada ? 'background:var(--color-celeste-muted);' : '' }}">
                    <td>
                        <div style="display:flex; align-items:center; gap:.6rem;">
                            @if($item['foto_path'])
                            <img src="{{ Storage::url($item['foto_path']) }}"
                                 style="width:36px; height:36px; object-fit:cover; border-radius:var(--radius-sm);">
                            @endif
                            <div>
                                <span style="font-weight:500;">{{ $item['nombre'] }}</span>
                                @if($esHelada)
                                <span style="font-size:.7rem; background:var(--color-celeste-dark); color:#fff;
                                             padding:.1rem .4rem; border-radius:var(--radius-pill); margin-left:.3rem;">
                                    🧊 helado
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-right" style="color:var(--color-text-muted); font-size:.82rem;">
                        S/ {{ number_format($item['precio_unitario'], 2) }}
                    </td>
                    <td class="text-center">
                        {{-- Toggle helada directo en carrito --}}
                        <label style="cursor:pointer; display:inline-flex; align-items:center; justify-content:center;
                                      width:28px; height:28px; border-radius:var(--radius-sm);
                                      background:{{ $esHelada ? 'var(--color-celeste-dark)' : 'var(--color-surface-2)' }};
                                      border:1.5px solid {{ $esHelada ? 'var(--color-celeste-dark)' : 'var(--color-border)' }};
                                      transition:all var(--transition-fast);">
                            <input type="checkbox"
                                   wire:model.live="heladasCarrito.{{ $i }}"
                                   style="display:none;">
                            <span style="font-size:.9rem;">🧊</span>
                        </label>
                    </td>
                    <td class="text-right" style="font-weight:600; color:{{ $esHelada ? 'var(--color-celeste-dark)' : 'var(--color-text-primary)' }};">
                        S/ {{ number_format($precioReal, 2) }}
                        @if($esHelada && $precioHelada > 0)
                        <div style="font-size:.7rem; color:var(--color-celeste-dark); font-weight:400;">
                            +S/ {{ number_format($precioHelada, 2) }}
                        </div>
                        @endif
                    </td>
                    <td class="text-right">
                        <span class="badge badge-info">{{ $item['cantidad'] }}</span>
                    </td>
                    <td class="text-right" style="font-weight:700;">
                        S/ {{ number_format($subtotalReal, 2) }}
                    </td>
                    <td class="text-center">
                        <button wire:click="quitarItem({{ $i }})" class="link-action danger">✕</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
                        <div style="font-size:2rem; margin-bottom:.5rem;">🛒</div>
                        Carrito vacío — escanea o escribe un código para comenzar
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(count($carrito))
            <tfoot>
                <tr style="background:var(--color-turquesa-muted); border-top:2px solid var(--color-border);">
                    <td colspan="5" style="text-align:right; font-weight:700; padding:.85rem 1rem;">TOTAL</td>
                    <td style="text-align:right; font-size:1.15rem; font-weight:800; color:var(--color-turquesa); padding:.85rem 1rem;">
                        S/ {{ number_format($total, 2) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if(count($carrito))
    <div style="display:flex; justify-content:flex-end;">
        <button wire:click="abrirCobro" class="btn btn-success"
                style="font-size:1rem; padding:.7rem 2.5rem;">
            Cobrar S/ {{ number_format($total, 2) }}
        </button>
    </div>
    @endif

    {{-- Modal cobro --}}
    @if($mostrarModalCobro)
    @teleport('body')
    <div class="modal-backdrop" style="z-index:999;">
        <div class="modal-box animate-scale-in" style="max-width:560px; width:100%;">
            <h2 class="modal-title">Cobrar Venta — S/ {{ number_format($total, 2) }}</h2>

            {{-- Resumen heladas en modal --}}
            @php
                $tieneHeladas = collect($heladasCarrito)->contains(true);
            @endphp
            <div style="margin-bottom:1.25rem; max-height:220px; overflow-y:auto;">
                <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted);
                           text-transform:uppercase; letter-spacing:.05em; margin-bottom:.5rem;">
                    Productos — marca los que van helados
                    @if($precioHelada > 0)
                    <span style="color:var(--color-celeste-dark);">(+S/ {{ number_format($precioHelada, 2) }} c/u)</span>
                    @endif
                </p>
                @foreach($carritoExpandido as $i => $item)
                @php $esH = $heladasCarrito[$i] ?? false; @endphp
                <label style="display:flex; align-items:center; gap:.75rem; padding:.5rem .65rem;
                               border-radius:var(--radius-md); cursor:pointer; margin-bottom:.3rem;
                               background:{{ $esH ? 'var(--color-celeste-muted)' : 'transparent' }};
                               border:1.5px solid {{ $esH ? 'var(--color-celeste-dark)' : 'var(--color-border)' }};
                               transition:all var(--transition-fast);">
                    <input type="checkbox" wire:model.live="heladasCarrito.{{ $i }}"
                           style="width:16px; height:16px; accent-color:var(--color-celeste-dark);">
                    <span style="flex:1; font-size:.875rem; font-weight:500;">
                        {{ $item['nombre'] }}
                        @if($esH)
                        <span style="font-size:.72rem; background:var(--color-celeste-dark); color:#fff;
                                     padding:.1rem .4rem; border-radius:99px; margin-left:.3rem;">🧊 helado</span>
                        @endif
                    </span>
                    <div style="text-align:right; font-size:.8rem;">
                        <div style="color:var(--color-text-muted);">x{{ $item['cantidad'] }}</div>
                        <div style="font-weight:700; color:{{ $esH ? 'var(--color-celeste-dark)' : 'var(--color-turquesa)' }};">
                            S/ {{ number_format(($item['precio_unitario'] + ($esH ? $precioHelada : 0)) * $item['cantidad'], 2) }}
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            {{-- Método de pago --}}
            <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted);
                       text-transform:uppercase; letter-spacing:.05em; margin-bottom:.5rem;">
                Método de pago
            </p>
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:.5rem; margin-bottom:1rem;">
                @foreach(['efectivo' => 'Efectivo', 'yape' => 'Yape', 'plin' => 'Plin', 'otro' => 'Otro', 'fiado' => '📒 Fiado'] as $val => $label)
                <button wire:click="$set('metodoPago', '{{ $val }}')"
                        style="padding:.55rem .4rem; border-radius:var(--radius-md); font-size:.8rem;
                               font-weight:600; cursor:pointer;
                               border:2px solid {{ $metodoPago === $val ? 'var(--color-turquesa)' : 'var(--color-border)' }};
                               background:{{ $metodoPago === $val ? 'var(--color-turquesa-muted)' : 'var(--color-surface)' }};
                               color:{{ $metodoPago === $val ? 'var(--color-turquesa-dark)' : 'var(--color-text-secondary)' }};
                               transition:all var(--transition-fast);">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- QR Yape/Plin --}}
            @if(in_array($metodoPago, ['yape','plin']))
            @php $comercio = \Illuminate\Support\Facades\DB::table('bodega.comercio')->where('estado',1)->where('id', Auth::user()->id_comercio)->first(); @endphp
            @if($comercio)
            <div style="text-align:center; margin-bottom:1rem;">
                @if($metodoPago === 'yape' && $comercio->yape_qr)
                    <img src="{{ Storage::url($comercio->yape_qr) }}"
                         style="max-height:150px; border-radius:var(--radius-md); border:2px solid var(--color-turquesa);">
                    <p style="font-size:.78rem; color:var(--color-text-muted); margin-top:.4rem;">QR Yape</p>
                @elseif($metodoPago === 'plin' && $comercio->plin_qr)
                    <img src="{{ Storage::url($comercio->plin_qr) }}"
                         style="max-height:150px; border-radius:var(--radius-md); border:2px solid #7c3aed;">
                    <p style="font-size:.78rem; color:var(--color-text-muted); margin-top:.4rem;">QR Plin</p>
                @endif
            </div>
            @endif
            @endif

            @if($metodoPago === 'fiado')
            <div class="animate-fade-in" style="padding:.75rem 1rem; border-radius:var(--radius-md);
                        background:var(--color-warning-light); border:1.5px solid var(--color-warning);">
                <label style="display:block; font-size:.78rem; font-weight:600; color:var(--color-warning); margin-bottom:.3rem;">
                    Cliente *
                </label>
                <select wire:model="clienteFiadoId" class="input" style="border-color:var(--color-warning);">
                    <option value="">— Seleccionar cliente —</option>
                    @foreach($clientesFiado as $cl)
                        <option value="{{ $cl['id'] }}">{{ $cl['nombre'] }} {{ $cl['telefono'] ? '· '.$cl['telefono'] : '' }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Efectivo + vuelto --}}
            @if($metodoPago === 'efectivo')
            <div style="display:flex; gap:.75rem; align-items:flex-end; margin-bottom:1rem;">
                <div style="flex:1;">
                    <label style="display:block; font-size:.82rem; font-weight:600;
                                   color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Efectivo recibido (S/)
                    </label>
                    <input wire:model.live="efectivoRecibido"
                           type="number" step="0.10" min="{{ $total }}"
                           class="input"
                           placeholder="0.00"
                           style="font-size:1.1rem; font-weight:700;">
                </div>
                <div style="flex:1; background:var(--color-turquesa-muted); border-radius:var(--radius-md);
                             padding:.75rem; text-align:center;">
                    <p style="font-size:.75rem; font-weight:600; color:var(--color-text-muted); margin-bottom:.2rem;">VUELTO</p>
                    <p style="font-size:1.6rem; font-weight:800;
                               color:{{ $this->calcularVuelto() >= 0 ? 'var(--color-turquesa)' : 'var(--color-danger)' }};">
                        S/ {{ number_format($this->calcularVuelto(), 2) }}
                    </p>
                </div>
            </div>
            @if($efectivoRecibido !== '' && (float)$efectivoRecibido < $total)
            <div class="alert alert-danger" style="margin-bottom:.75rem;">El efectivo no cubre el total.</div>
            @endif
            @endif

            {{-- Total final --}}
            <div style="text-align:right; padding:.65rem .85rem; background:var(--color-turquesa-muted);
                        border-radius:var(--radius-md); margin-bottom:.75rem; border:1.5px solid var(--color-turquesa-light);">
                <span style="font-size:.82rem; color:var(--color-text-muted);">Total a cobrar:</span>
                <span style="font-size:1.3rem; font-weight:800; color:var(--color-turquesa); margin-left:.5rem;">
                    S/ {{ number_format($total, 2) }}
                </span>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.65rem;">
                <button wire:click="$set('mostrarModalCobro', false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="registrarVenta"
                    @if($metodoPago === 'efectivo' && (float)$efectivoRecibido < $total) disabled
                    @elseif($metodoPago === 'fiado' && !$clienteFiadoId) disabled
                    @endif
                    class="btn btn-success">
                @if($metodoPago === 'fiado') 📒 Confirmar Fiado
                @else Confirmar Venta
                @endif
            </button>
            </div>
        </div>
    </div>
    @endteleport
    @endif
</div>

@push('scripts')
<script>
function escaner(livewire) {
    return {
        buffer: '',
        activo: false,
        timer: null,

        init() {
            this.$nextTick(() => this.activar());

            this.$el.addEventListener('click', (e) => {
                const tag = e.target.tagName.toLowerCase();
                if (!['input','button','select','textarea','a','label'].includes(tag)) {
                    this.activar();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (!this.activo) return;

                const foco = document.activeElement;
                const tag  = foco ? foco.tagName.toLowerCase() : '';
                if (['input','textarea','select'].includes(tag) && foco.id !== 'input-escaner-oculto') return;

                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.disparar();
                    return;
                }

                if (e.key.length === 1) {
                    this.buffer += e.key;
                    this.reiniciarTimer();
                }
            });
        },

        activar() {
            this.activo = true;
            this.$refs.inputOculto && this.$refs.inputOculto.focus();
        },

        reiniciarTimer() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                if (this.buffer.length >= 3) this.disparar();
            }, 300);
        },

        disparar() {
            clearTimeout(this.timer);
            const codigo = this.buffer.trim();
            this.buffer  = '';
            if (codigo.length >= 2) {
                livewire.buscarPorBuffer(codigo);
            }
        },
    };
}
</script>
@endpush