<div>
    {{-- ── Flash ── --}}
    @if(session('ok'))
        <div class="alert alert-success animate-fade-in" style="margin-bottom:1.25rem;">
            <span>✓</span> {{ session('ok') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════
         CABECERA
    ══════════════════════════════════════════ --}}
    <div class="page-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.5rem;">
        <div>
            <h1 class="page-title" style="margin:0; font-size:1.35rem; font-weight:700;">
                📒 Fiados
            </h1>
            <p style="margin:.15rem 0 0; font-size:.82rem; color:var(--color-text-secondary);">
                Cuentas pendientes por cobrar
            </p>
        </div>

        {{-- Resumen rápido --}}
        @php
            $totalDeuda = $fiados->where('estado', 1)->sum('saldo');
        @endphp
        @if($totalDeuda > 0)
        <div style="background:var(--color-danger-light,#fff0f0); border:1.5px solid var(--color-danger); border-radius:var(--radius-md); padding:.5rem 1rem; text-align:right;">
            <div style="font-size:.72rem; color:var(--color-danger); font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Saldo pendiente (esta página)</div>
            <div style="font-size:1.25rem; font-weight:800; color:var(--color-danger);">S/ {{ number_format($totalDeuda, 2) }}</div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         FILTROS
    ══════════════════════════════════════════ --}}
    <div class="card" style="padding:.85rem 1rem; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; align-items:center; gap:.75rem;">

        {{-- Tabs de estado --}}
        <div style="display:flex; gap:.3rem; flex-wrap:wrap;">
            @foreach([['1','Activos','var(--color-warning)'], ['2','Pagados','var(--color-success)'], ['0','Cancelados','var(--color-text-secondary)'], ['','Todos','']] as [$val, $label, $color])
            <button wire:click="$set('filtroEstado', '{{ $val }}')"
                    style="padding:.3rem .85rem; border-radius:999px; font-size:.78rem; font-weight:600; border:1.5px solid;
                           border-color:{{ $filtroEstado === $val ? $color ?: 'var(--color-primary)' : 'var(--color-border)' }};
                           background:{{ $filtroEstado === $val ? ($color ? $color.'22' : 'var(--color-primary-light)') : 'transparent' }};
                           color:{{ $filtroEstado === $val ? ($color ?: 'var(--color-primary)') : 'var(--color-text-secondary)' }};
                           cursor:pointer; transition:all var(--transition-base);">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Buscador --}}
        <div style="position:relative; flex:1; min-width:200px; max-width:320px;">
            <svg style="position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:var(--color-text-secondary); pointer-events:none;"
                 width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    <div class="card" style="overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:right;">Pagado</th>
                        <th style="text-align:right;">Saldo</th>
                        <th>Estado</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fiados as $f)
                        {{-- ── Fila principal ── --}}
                        <tr wire:key="fiado-{{ $f->id }}"
                            style="{{ $fiadoDetalleId === $f->id ? 'background:var(--color-surface-2)' : '' }}">

                            <td style="color:var(--color-text-secondary); font-size:.8rem;">{{ $f->id }}</td>

                            <td>
                                <div style="font-weight:600; font-size:.9rem;">{{ $f->cliente->nombre }}</div>
                                @if($f->cliente->telefono)
                                    <div style="font-size:.75rem; color:var(--color-text-secondary);">{{ $f->cliente->telefono }}</div>
                                @endif
                            </td>

                            <td style="font-size:.82rem; color:var(--color-text-secondary); white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($f->fecha_creacion)->format('d/m/Y H:i') }}
                            </td>

                            <td style="text-align:right; font-weight:600; white-space:nowrap;">
                                S/ {{ number_format($f->total, 2) }}
                            </td>

                            <td style="text-align:right; color:var(--color-success); white-space:nowrap;">
                                S/ {{ number_format($f->total_pagado, 2) }}
                            </td>

                            <td style="text-align:right; white-space:nowrap;">
                                @if($f->saldo > 0)
                                    <span style="color:var(--color-danger); font-weight:700; font-size:.95rem;">
                                        S/ {{ number_format($f->saldo, 2) }}
                                    </span>
                                @else
                                    <span style="color:var(--color-success); font-weight:600;">S/ 0.00</span>
                                @endif
                            </td>

                            <td>
                                @if($f->estado == 1)
                                    <span class="badge badge-warning" style="font-size:.72rem;">Pendiente</span>
                                @elseif($f->estado == 2)
                                    <span class="badge badge-success" style="font-size:.72rem;">Pagado</span>
                                @else
                                    <span class="badge" style="font-size:.72rem; background:var(--color-surface-3); color:var(--color-text-secondary);">Cancelado</span>
                                @endif
                            </td>

                            <td style="text-align:right;">
                                <div style="display:flex; gap:.35rem; justify-content:flex-end; flex-wrap:nowrap;">
                                    {{-- Ver detalle --}}
                                    <button wire:click="verDetalle({{ $f->id }})"
                                            class="btn btn-secondary btn-sm"
                                            title="{{ $fiadoDetalleId === $f->id ? 'Ocultar' : 'Ver detalle' }}"
                                            style="{{ $fiadoDetalleId === $f->id ? 'background:var(--color-primary); color:#fff; border-color:var(--color-primary);' : '' }}">
                                        @if($fiadoDetalleId === $f->id)
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                                        @else
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                        @endif
                                    </button>

                                    {{-- Abonar (solo si activo y tiene saldo) --}}
                                    @if($f->estado == 1 && $f->saldo > 0)
                                    <button wire:click="abrirPago({{ $f->id }})"
                                            class="btn btn-primary btn-sm"
                                            title="Registrar pago">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    </button>
                                    @endif

                                    {{-- Cancelar (solo si activo) --}}
                                    @if($f->estado == 1)
                                    <button wire:click="cancelarFiado({{ $f->id }})"
                                            wire:confirm="¿Cancelar este fiado? Se marcará como cancelado sin cobrar el saldo pendiente."
                                            class="btn btn-danger btn-sm"
                                            title="Cancelar fiado">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- ── Fila de detalle expandible ── --}}
                        @if($fiadoDetalleId === $f->id)
                        <tr wire:key="detalle-{{ $f->id }}">
                            <td colspan="8" style="padding:0; background:var(--color-surface-2); border-top:none;">
                                <div class="animate-fade-in" style="padding:1rem 1.5rem 1.25rem; display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">

                                    {{-- Productos --}}
                                    <div>
                                        <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-text-secondary); margin-bottom:.6rem;">
                                            Productos fiados
                                        </div>
                                        @if(count($detalleProductos) > 0)
                                        <table style="width:100%; border-collapse:collapse; font-size:.83rem;">
                                            <thead>
                                                <tr style="border-bottom:1px solid var(--color-border);">
                                                    <th style="text-align:left; padding:.3rem .4rem; color:var(--color-text-secondary); font-weight:600; font-size:.75rem;">Producto</th>
                                                    <th style="text-align:center; padding:.3rem .4rem; color:var(--color-text-secondary); font-weight:600; font-size:.75rem;">Cant.</th>
                                                    <th style="text-align:right; padding:.3rem .4rem; color:var(--color-text-secondary); font-weight:600; font-size:.75rem;">Precio</th>
                                                    <th style="text-align:right; padding:.3rem .4rem; color:var(--color-text-secondary); font-weight:600; font-size:.75rem;">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($detalleProductos as $item)
                                                <tr style="border-bottom:1px solid var(--color-border);">
                                                    <td style="padding:.35rem .4rem;">
                                                        {{ $item->producto }}
                                                        @if($item->es_helada)
                                                            <span style="font-size:.7rem; background:var(--color-primary-light,#e0f0ff); color:var(--color-primary); padding:.1rem .35rem; border-radius:4px; margin-left:.3rem;">🧊 fría</span>
                                                        @endif
                                                    </td>
                                                    <td style="text-align:center; padding:.35rem .4rem;">{{ $item->cantidad }}</td>
                                                    <td style="text-align:right; padding:.35rem .4rem;">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                                                    <td style="text-align:right; padding:.35rem .4rem; font-weight:600;">S/ {{ number_format($item->subtotal, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                            <p style="font-size:.82rem; color:var(--color-text-secondary);">Sin productos registrados</p>
                                        @endif
                                    </div>

                                    {{-- Pagos realizados --}}
                                    <div>
                                        <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-text-secondary); margin-bottom:.6rem;">
                                            Pagos realizados
                                        </div>
                                        @if(count($detallePagos) > 0)
                                        <table style="width:100%; border-collapse:collapse; font-size:.83rem;">
                                            <thead>
                                                <tr style="border-bottom:1px solid var(--color-border);">
                                                    <th style="text-align:left; padding:.3rem .4rem; color:var(--color-text-secondary); font-weight:600; font-size:.75rem;">Fecha</th>
                                                    <th style="text-align:left; padding:.3rem .4rem; color:var(--color-text-secondary); font-weight:600; font-size:.75rem;">Método</th>
                                                    <th style="text-align:right; padding:.3rem .4rem; color:var(--color-text-secondary); font-weight:600; font-size:.75rem;">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($detallePagos as $pago)
                                                <tr style="border-bottom:1px solid var(--color-border);">
                                                    <td style="padding:.35rem .4rem; font-size:.78rem; color:var(--color-text-secondary);">
                                                        {{ \Carbon\Carbon::parse($pago->fecha_creacion)->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td style="padding:.35rem .4rem;">
                                                        <span style="text-transform:capitalize;">{{ $pago->metodo_pago }}</span>
                                                        @if($pago->notas)
                                                            <div style="font-size:.72rem; color:var(--color-text-secondary);">{{ $pago->notas }}</div>
                                                        @endif
                                                    </td>
                                                    <td style="text-align:right; padding:.35rem .4rem; color:var(--color-success); font-weight:700;">
                                                        + S/ {{ number_format($pago->monto, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                            <p style="font-size:.82rem; color:var(--color-text-secondary);">Sin pagos aún</p>
                                        @endif

                                        {{-- Barra de progreso del pago --}}
                                        @php $pct = $f->total > 0 ? min(100, round($f->total_pagado / $f->total * 100)) : 0; @endphp
                                        <div style="margin-top:.85rem;">
                                            <div style="display:flex; justify-content:space-between; font-size:.75rem; color:var(--color-text-secondary); margin-bottom:.3rem;">
                                                <span>Progreso de pago</span>
                                                <span>{{ $pct }}%</span>
                                            </div>
                                            <div style="height:6px; background:var(--color-border); border-radius:99px; overflow:hidden;">
                                                <div style="height:100%; width:{{ $pct }}%; background:{{ $pct >= 100 ? 'var(--color-success)' : 'var(--color-warning)' }}; border-radius:99px; transition:width .4s ease;"></div>
                                            </div>
                                            <div style="display:flex; justify-content:space-between; font-size:.72rem; margin-top:.25rem; color:var(--color-text-secondary);">
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
                            <td colspan="8" style="text-align:center; padding:2.5rem; color:var(--color-text-secondary); font-size:.88rem;">
                                No hay fiados
                                @if($filtroEstado === '1') activos @elseif($filtroEstado === '2') pagados @elseif($filtroEstado === '0') cancelados @endif
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
    @php $fiado = $fiados->firstWhere('id', $fiadoPagandoId) ?? \App\Models\Fiado::find($fiadoPagandoId); @endphp
    <div class="modal-backdrop animate-fade-in" wire:click.self="$set('fiadoPagandoId', null)">
        <div class="modal animate-slide-up" style="max-width:440px; width:100%;">

            <div class="modal-header">
                <div>
                    <h2 class="modal-title" style="margin:0;">Registrar pago</h2>
                    @if($fiado)
                    <div style="font-size:.8rem; color:var(--color-text-secondary); margin-top:.15rem;">
                        {{ $fiado->cliente->nombre }} · Saldo: <strong style="color:var(--color-danger);">S/ {{ number_format($fiado->saldo, 2) }}</strong>
                    </div>
                    @endif
                </div>
                <button wire:click="$set('fiadoPagandoId', null)" class="modal-close" aria-label="Cerrar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="modal-body" style="display:flex; flex-direction:column; gap:1rem;">

                {{-- Monto --}}
                <div class="form-group">
                    <label class="form-label">Monto a pagar <span style="color:var(--color-danger)">*</span></label>
                    <div style="position:relative;">
                        <span style="position:absolute; left:.85rem; top:50%; transform:translateY(-50%); font-weight:700; color:var(--color-text-secondary);">S/</span>
                        <input wire:model="montoPago" type="number" step="0.01" min="0.01"
                               class="input @error('montoPago') input-error @enderror"
                               style="padding-left:2.2rem;"
                               placeholder="0.00" autofocus>
                    </div>
                    @error('montoPago')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    {{-- Botón pagar total --}}
                    @if($fiado)
                    <button wire:click="$set('montoPago', '{{ $fiado->saldo }}')"
                            type="button"
                            style="margin-top:.4rem; font-size:.75rem; color:var(--color-primary); background:none; border:none; cursor:pointer; padding:0; text-decoration:underline;">
                        Pagar total (S/ {{ number_format($fiado->saldo, 2) }})
                    </button>
                    @endif
                </div>

                {{-- Método de pago --}}
                <div class="form-group">
                    <label class="form-label">Método de pago</label>
                    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:.4rem;">
                        @foreach(['efectivo' => '💵 Efectivo', 'yape' => '📱 Yape', 'plin' => '📲 Plin', 'otro' => '🔄 Otro'] as $val => $label)
                        <button wire:click="$set('metodoPagoPago', '{{ $val }}')"
                                type="button"
                                style="padding:.45rem .3rem; border-radius:var(--radius-sm); font-size:.76rem; font-weight:600; border:1.5px solid;
                                       border-color:{{ $metodoPagoPago === $val ? 'var(--color-primary)' : 'var(--color-border)' }};
                                       background:{{ $metodoPagoPago === $val ? 'var(--color-primary-light,#e0f0ff)' : 'transparent' }};
                                       color:{{ $metodoPagoPago === $val ? 'var(--color-primary)' : 'var(--color-text-secondary)' }};
                                       cursor:pointer; text-align:center; transition:all var(--transition-base);">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Notas --}}
                <div class="form-group">
                    <label class="form-label">Nota (opcional)</label>
                    <input wire:model="notasPago" type="text" class="input"
                           placeholder="Ej: abono parcial, día de pago...">
                </div>

            </div>

            <div class="modal-footer">
                <button wire:click="$set('fiadoPagandoId', null)" class="btn btn-secondary">
                    Cancelar
                </button>
                <button wire:click="registrarPago" wire:loading.attr="disabled" class="btn btn-success">
                    <span wire:loading.remove wire:target="registrarPago">Confirmar pago</span>
                    <span wire:loading wire:target="registrarPago">Registrando…</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>