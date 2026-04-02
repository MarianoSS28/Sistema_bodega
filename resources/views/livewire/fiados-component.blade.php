<div>
    @if(session('ok'))
        <div class="alert alert-success animate-fade-in" style="margin-bottom:1.25rem;">
            ✓ {{ session('ok') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════
         CABECERA
    ══════════════════════════════════════════ --}}
    <div style="display:flex; align-items:center; justify-content:space-between;
                flex-wrap:wrap; gap:.75rem; margin-bottom:1.5rem;">
        <div>
            <h1 class="page-title" style="margin:0;">📒 Fiados</h1>
            <p style="margin:.15rem 0 0; font-size:.82rem; color:var(--color-text-secondary);">
                Cuentas pendientes por cobrar
            </p>
        </div>

        @php
            $totalDeudaPagina = $fiados->where('estado', 1)->sum('saldo');
        @endphp
        @if($totalDeudaPagina > 0)
        <div style="background:var(--color-danger-light); border:1.5px solid var(--color-danger);
                    border-radius:var(--radius-md); padding:.5rem 1rem; text-align:right;">
            <div style="font-size:.72rem; color:var(--color-danger); font-weight:600;
                         text-transform:uppercase; letter-spacing:.04em;">Saldo pendiente (esta página)</div>
            <div style="font-size:1.25rem; font-weight:800; color:var(--color-danger);">
                S/ {{ number_format($totalDeudaPagina, 2) }}
            </div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         FILTROS
    ══════════════════════════════════════════ --}}
    <div class="card" style="padding:.85rem 1rem; margin-bottom:1.25rem;
                              display:flex; flex-wrap:wrap; align-items:center; gap:.75rem;">

        {{-- Tabs de estado --}}
        <div style="display:flex; gap:.3rem; flex-wrap:wrap;">
            @foreach([
                ['1',  'Activos',    'var(--color-warning)'],
                ['2',  'Pagados',    'var(--color-turquesa)'],
                ['0',  'Cancelados', 'var(--color-text-secondary)'],
                ['',   'Todos',      'var(--color-celeste-dark)'],
            ] as [$val, $label, $color])
            <button wire:click="$set('filtroEstado', '{{ $val }}')"
                    style="padding:.3rem .85rem; border-radius:999px; font-size:.78rem;
                           font-weight:600; border:1.5px solid; cursor:pointer;
                           transition:all var(--transition-base);
                           border-color:{{ $filtroEstado === $val ? $color : 'var(--color-border)' }};
                           background:{{ $filtroEstado === $val ? $color.'22' : 'transparent' }};
                           color:{{ $filtroEstado === $val ? $color : 'var(--color-text-secondary)' }};">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Buscador --}}
        <div style="position:relative; flex:1; min-width:200px; max-width:320px;">
            <svg style="position:absolute; left:.75rem; top:50%; transform:translateY(-50%);
                         color:var(--color-text-secondary); pointer-events:none;"
                 width="13" height="13" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input wire:model.live.debounce.300ms="busqueda"
                   type="text" placeholder="Buscar cliente…"
                   class="input" style="padding-left:2.2rem; font-size:.85rem;">
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TABLA DE FIADOS
    ══════════════════════════════════════════ --}}
    <div class="table-wrap animate-fade-in delay-100">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px;">N°</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Pagado</th>
                        <th class="text-right">Saldo</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center" style="width:130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fiados as $f)

                    {{-- ── Fila principal ── --}}
                    <tr wire:key="fiado-{{ $f->id }}"
                        style="{{ $fiadoDetalleId === $f->id ? 'background:var(--color-turquesa-muted);' : '' }}">

                        {{-- N° por comercio --}}
                        <td>
                            <span style="font-family:monospace; font-size:.82rem; font-weight:700;
                                         color:var(--color-turquesa);">
                                #{{ $f->nro_fiado ?? $f->id }}
                            </span>
                        </td>

                        {{-- Cliente --}}
                        <td>
                            <div style="font-weight:600; font-size:.9rem;">{{ $f->cliente->nombre }}</div>
                            @if($f->cliente->telefono)
                                <div style="font-size:.75rem; color:var(--color-text-secondary);">
                                    {{ $f->cliente->telefono }}
                                </div>
                            @endif
                        </td>

                        {{-- Fecha --}}
                        <td style="font-size:.82rem; color:var(--color-text-secondary); white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($f->fecha_creacion)->format('d/m/Y H:i') }}
                        </td>

                        {{-- Total --}}
                        <td class="text-right" style="font-weight:600; white-space:nowrap;">
                            S/ {{ number_format($f->total, 2) }}
                        </td>

                        {{-- Pagado --}}
                        <td class="text-right" style="white-space:nowrap;">
                            <span style="color:var(--color-turquesa); font-weight:600;">
                                S/ {{ number_format($f->total_pagado, 2) }}
                            </span>
                        </td>

                        {{-- Saldo --}}
                        <td class="text-right" style="white-space:nowrap;">
                            @if($f->saldo > 0)
                                <span style="color:var(--color-danger); font-weight:700; font-size:.95rem;">
                                    S/ {{ number_format($f->saldo, 2) }}
                                </span>
                            @else
                                <span style="color:var(--color-turquesa); font-weight:600;">S/ 0.00</span>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td class="text-center">
                            @if($f->estado == 1)
                                <span class="badge badge-warning" style="font-size:.72rem;">Pendiente</span>
                            @elseif($f->estado == 2)
                                <span class="badge badge-success" style="font-size:.72rem;">Pagado</span>
                            @else
                                <span class="badge" style="font-size:.72rem; background:var(--color-surface-2);
                                                            color:var(--color-text-secondary);">Cancelado</span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="text-center">
                            <div style="display:flex; gap:.3rem; justify-content:center; flex-wrap:nowrap;">

                                {{-- Ver detalle --}}
                                <button wire:click="verDetalle({{ $f->id }})"
                                        class="btn btn-secondary"
                                        style="padding:.3rem .6rem; font-size:.78rem;"
                                        title="{{ $fiadoDetalleId === $f->id ? 'Ocultar' : 'Ver detalle' }}">
                                    @if($fiadoDetalleId === $f->id)
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                                    @else
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                    @endif
                                </button>

                                {{-- Abonar --}}
                                @if($f->estado == 1 && $f->saldo > 0)
                                <button wire:click="abrirPago({{ $f->id }})"
                                        class="btn btn-primary"
                                        style="padding:.3rem .6rem; font-size:.78rem;"
                                        title="Registrar pago">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="1" x2="12" y2="23"/>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </button>
                                @endif

                                {{-- Cancelar --}}
                                @if($f->estado == 1)
                                <button wire:click="cancelarFiado({{ $f->id }})"
                                        wire:confirm="¿Cancelar este fiado? Se marcará como cancelado sin cobrar el saldo pendiente."
                                        class="btn btn-danger"
                                        style="padding:.3rem .6rem; font-size:.78rem;"
                                        title="Cancelar fiado">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                    </svg>
                                </button>
                                @endif

                            </div>
                        </td>
                    </tr>

                    {{-- ── Fila de detalle expandible ── --}}
                    @if($fiadoDetalleId === $f->id)
                    <tr wire:key="detalle-{{ $f->id }}">
                        <td colspan="8" style="padding:0; background:var(--color-surface-2); border-top:none;">
                            <div class="animate-fade-in"
                                 style="padding:1rem 1.5rem 1.25rem;
                                        display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">

                                {{-- Productos --}}
                                <div>
                                    <p style="font-size:.75rem; font-weight:700; text-transform:uppercase;
                                               letter-spacing:.06em; color:var(--color-text-secondary);
                                               margin-bottom:.6rem;">
                                        Productos fiados
                                    </p>
                                    @if(count($detalleProductos) > 0)
                                    <table style="width:100%; border-collapse:collapse; font-size:.83rem;">
                                        <thead>
                                            <tr style="border-bottom:1px solid var(--color-border);">
                                                <th style="text-align:left; padding:.3rem .4rem;
                                                            color:var(--color-text-secondary); font-weight:600;
                                                            font-size:.75rem;">Producto</th>
                                                <th style="text-align:center; padding:.3rem .4rem;
                                                            color:var(--color-text-secondary); font-weight:600;
                                                            font-size:.75rem;">Cant.</th>
                                                <th style="text-align:right; padding:.3rem .4rem;
                                                            color:var(--color-text-secondary); font-weight:600;
                                                            font-size:.75rem;">Precio</th>
                                                <th style="text-align:right; padding:.3rem .4rem;
                                                            color:var(--color-text-secondary); font-weight:600;
                                                            font-size:.75rem;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detalleProductos as $item)
                                            <tr style="border-bottom:1px solid var(--color-border);">
                                                <td style="padding:.35rem .4rem;">
                                                    {{ $item->producto }}
                                                    @if($item->es_helada)
                                                        <span style="font-size:.7rem; background:var(--color-celeste-muted);
                                                                      color:var(--color-celeste-dark); padding:.1rem .35rem;
                                                                      border-radius:4px; margin-left:.3rem;">🧊 fría</span>
                                                    @endif
                                                </td>
                                                <td style="text-align:center; padding:.35rem .4rem;">
                                                    {{ $item->cantidad }}
                                                </td>
                                                <td style="text-align:right; padding:.35rem .4rem;">
                                                    S/ {{ number_format($item->precio_unitario, 2) }}
                                                </td>
                                                <td style="text-align:right; padding:.35rem .4rem; font-weight:600;">
                                                    S/ {{ number_format($item->subtotal, 2) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                        <p style="font-size:.82rem; color:var(--color-text-secondary);">
                                            Sin productos registrados
                                        </p>
                                    @endif
                                </div>

                                {{-- Pagos realizados --}}
                                <div>
                                    <p style="font-size:.75rem; font-weight:700; text-transform:uppercase;
                                               letter-spacing:.06em; color:var(--color-text-secondary);
                                               margin-bottom:.6rem;">
                                        Pagos realizados
                                    </p>
                                    @if(count($detallePagos) > 0)
                                    <table style="width:100%; border-collapse:collapse; font-size:.83rem;">
                                        <thead>
                                            <tr style="border-bottom:1px solid var(--color-border);">
                                                <th style="text-align:left; padding:.3rem .4rem;
                                                            color:var(--color-text-secondary); font-weight:600;
                                                            font-size:.75rem;">Fecha</th>
                                                <th style="text-align:left; padding:.3rem .4rem;
                                                            color:var(--color-text-secondary); font-weight:600;
                                                            font-size:.75rem;">Método</th>
                                                <th style="text-align:right; padding:.3rem .4rem;
                                                            color:var(--color-text-secondary); font-weight:600;
                                                            font-size:.75rem;">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detallePagos as $pago)
                                            <tr style="border-bottom:1px solid var(--color-border);">
                                                <td style="padding:.35rem .4rem; font-size:.78rem;
                                                            color:var(--color-text-secondary); white-space:nowrap;">
                                                    {{ \Carbon\Carbon::parse($pago->fecha_creacion)->format('d/m/Y H:i') }}
                                                </td>
                                                <td style="padding:.35rem .4rem;">
                                                    <span style="text-transform:capitalize;">{{ $pago->metodo_pago }}</span>
                                                    @if($pago->notas)
                                                        <div style="font-size:.72rem; color:var(--color-text-secondary);">
                                                            {{ $pago->notas }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td style="text-align:right; padding:.35rem .4rem;
                                                            color:var(--color-turquesa); font-weight:700;">
                                                    + S/ {{ number_format($pago->monto, 2) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                        <p style="font-size:.82rem; color:var(--color-text-secondary);">
                                            Sin pagos aún
                                        </p>
                                    @endif

                                    {{-- Barra de progreso --}}
                                    @php
                                        $pct = $f->total > 0
                                            ? min(100, round($f->total_pagado / $f->total * 100))
                                            : 0;
                                    @endphp
                                    <div style="margin-top:.85rem;">
                                        <div style="display:flex; justify-content:space-between;
                                                     font-size:.75rem; color:var(--color-text-secondary);
                                                     margin-bottom:.3rem;">
                                            <span>Progreso de pago</span>
                                            <span>{{ $pct }}%</span>
                                        </div>
                                        <div style="height:6px; background:var(--color-border);
                                                     border-radius:99px; overflow:hidden;">
                                            <div style="height:100%; width:{{ $pct }}%;
                                                         background:{{ $pct >= 100 ? 'var(--color-turquesa)' : 'var(--color-warning)' }};
                                                         border-radius:99px; transition:width .4s ease;">
                                            </div>
                                        </div>
                                        <div style="display:flex; justify-content:space-between;
                                                     font-size:.72rem; margin-top:.25rem;
                                                     color:var(--color-text-secondary);">
                                            <span>Pagado: S/ {{ number_format($f->total_pagado, 2) }}</span>
                                            <span>Saldo: S/ {{ number_format($f->saldo, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                    @endif

                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:2.5rem;
                                               color:var(--color-text-secondary); font-size:.88rem;">
                            <div style="font-size:2rem; margin-bottom:.5rem;">📒</div>
                            No hay fiados
                            @if($filtroEstado === '1') activos
                            @elseif($filtroEstado === '2') pagados
                            @elseif($filtroEstado === '0') cancelados
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($fiados->hasPages())
            <div style="padding:.85rem 1rem; border-top:1px solid var(--color-border);">
                {{ $fiados->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         MODAL DE PAGO
    ══════════════════════════════════════════ --}}
    @if($fiadoPagandoId)
    @php
        $fiadoModal = $fiados->firstWhere('id', $fiadoPagandoId)
            ?? \App\Models\Fiado::find($fiadoPagandoId);
    @endphp
    @teleport('body')
    <div class="modal-backdrop animate-fade-in" wire:click.self="$set('fiadoPagandoId', null)">
        <div class="modal-box animate-scale-in" style="max-width:440px; width:100%;">

            {{-- Header --}}
            <div style="display:flex; justify-content:space-between; align-items:flex-start;
                         margin-bottom:1.25rem;">
                <div>
                    <h2 class="modal-title" style="margin:0;">Registrar pago</h2>
                    @if($fiadoModal)
                    <div style="font-size:.82rem; color:var(--color-text-secondary); margin-top:.2rem;">
                        <strong>{{ $fiadoModal->cliente->nombre }}</strong>
                        · Saldo:
                        <strong style="color:var(--color-danger);">
                            S/ {{ number_format($fiadoModal->saldo, 2) }}
                        </strong>
                    </div>
                    @endif
                </div>
                <button wire:click="$set('fiadoPagandoId', null)"
                        style="background:none; border:none; cursor:pointer;
                               color:var(--color-text-secondary); padding:.25rem;
                               border-radius:var(--radius-sm); line-height:1;"
                        aria-label="Cerrar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Campos --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">

                {{-- Monto --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600;
                                   color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Monto a pagar <span style="color:var(--color-danger)">*</span>
                    </label>
                    <div style="position:relative;">
                        <span style="position:absolute; left:.85rem; top:50%; transform:translateY(-50%);
                                      font-weight:700; color:var(--color-text-secondary);">S/</span>
                        <input wire:model="montoPago"
                               type="number" step="0.01" min="0.01"
                               class="input"
                               style="padding-left:2.2rem;"
                               placeholder="0.00"
                               autofocus>
                    </div>
                    @error('montoPago')
                        <span style="color:var(--color-danger); font-size:.78rem; margin-top:.2rem; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                    @if($fiadoModal)
                    <button wire:click="$set('montoPago', '{{ $fiadoModal->saldo }}')"
                            type="button"
                            style="margin-top:.35rem; font-size:.75rem; color:var(--color-turquesa);
                                   background:none; border:none; cursor:pointer; padding:0;
                                   text-decoration:underline; text-underline-offset:2px;">
                        Pagar total (S/ {{ number_format($fiadoModal->saldo, 2) }})
                    </button>
                    @endif
                </div>

                {{-- Método de pago --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600;
                                   color:var(--color-text-secondary); margin-bottom:.4rem;">
                        Método de pago
                    </label>
                    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:.4rem;">
                        @foreach([
                            'efectivo' => '💵 Efectivo',
                            'yape'     => '📱 Yape',
                            'plin'     => '📲 Plin',
                            'otro'     => '🔄 Otro',
                        ] as $val => $label)
                        <button wire:click="$set('metodoPagoPago', '{{ $val }}')"
                                type="button"
                                style="padding:.45rem .3rem; border-radius:var(--radius-sm);
                                       font-size:.76rem; font-weight:600; cursor:pointer;
                                       border:1.5px solid;
                                       border-color:{{ $metodoPagoPago === $val ? 'var(--color-turquesa)' : 'var(--color-border)' }};
                                       background:{{ $metodoPagoPago === $val ? 'var(--color-turquesa-muted)' : 'transparent' }};
                                       color:{{ $metodoPagoPago === $val ? 'var(--color-turquesa-dark)' : 'var(--color-text-secondary)' }};
                                       text-align:center; transition:all var(--transition-base);">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Notas --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600;
                                   color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Nota (opcional)
                    </label>
                    <input wire:model="notasPago"
                           type="text"
                           class="input"
                           placeholder="Ej: abono parcial, día de pago...">
                </div>

            </div>

            {{-- Footer --}}
            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                <button wire:click="$set('fiadoPagandoId', null)" class="btn btn-secondary">
                    Cancelar
                </button>
                <button wire:click="registrarPago"
                        wire:loading.attr="disabled"
                        class="btn btn-success">
                    <span wire:loading.remove wire:target="registrarPago">Confirmar pago</span>
                    <span wire:loading wire:target="registrarPago">Registrando…</span>
                </button>
            </div>

        </div>
    </div>
    @endteleport
    @endif

</div>