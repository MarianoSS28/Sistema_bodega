<div class="animate-fade-in">
    <h1 class="page-title">Historial de Ventas</h1>

    {{-- Filtros --}}
    <div class="card" style="padding:1.15rem 1.25rem; margin-bottom:1.25rem;">
        <div style="display:flex; flex-wrap:wrap; gap:.85rem; align-items:flex-end;">
            <div>
                <label style="display:block; font-size:.78rem; font-weight:600; color:var(--color-text-muted); margin-bottom:.3rem;">DESDE</label>
                <input wire:model.live="fechaDesde" type="date" class="input" style="width:auto;">
            </div>
            <div>
                <label style="display:block; font-size:.78rem; font-weight:600; color:var(--color-text-muted); margin-bottom:.3rem;">HASTA</label>
                <input wire:model.live="fechaHasta" type="date" class="input" style="width:auto;">
            </div>
            <div style="flex:1; min-width:180px;">
                <label style="display:block; font-size:.78rem; font-weight:600; color:var(--color-text-muted); margin-bottom:.3rem;">PRODUCTO</label>
                <input wire:model.live="filtroProducto" placeholder="Filtrar por producto..." class="input">
            </div>
            <div style="display:flex; gap:.5rem;">
                <button wire:click="abrirModalExport('excel')" class="btn btn-success">⬇ Excel</button>
                <button wire:click="abrirModalExport('pdf')"   class="btn btn-danger">⬇ PDF</button>
            </div>
        </div>
    </div>

    {{-- Modal selector Detallado / Resumen --}}
    @if($mostrarModalExport)
    @teleport('body')
    <div class="modal-backdrop" style="z-index:200;">
        <div class="modal-box animate-scale-in" style="max-width:400px;">
            <h2 class="modal-title" style="margin-bottom:.5rem;">
                {{ $tipoExport === 'excel' ? '📊 Exportar Excel' : '📄 Exportar PDF' }}
            </h2>
            <p style="font-size:.85rem; color:var(--color-text-secondary); margin-bottom:1.5rem;">
                Elige el tipo de exportación:
            </p>

            <div style="display:flex; flex-direction:column; gap:.75rem; margin-bottom:1.5rem;">
                {{-- Opción Detallado --}}
                <button wire:click="exportar('detallado')"
                        style="
                            display: flex; align-items: flex-start; gap: 1rem;
                            padding: 1rem 1.25rem;
                            border: 2px solid var(--color-turquesa);
                            border-radius: var(--radius-lg);
                            background: var(--color-turquesa-muted);
                            cursor: pointer; text-align: left;
                            transition: all var(--transition-base);
                            width: 100%;
                        "
                        onmouseover="this.style.background='var(--color-turquesa-light)'; this.style.transform='translateY(-1px)';"
                        onmouseout="this.style.background='var(--color-turquesa-muted)'; this.style.transform='';">
                    <span style="font-size:1.5rem; flex-shrink:0;">📋</span>
                    <div>
                        <p style="font-weight:700; font-size:.9rem; color:var(--color-turquesa-dark); margin-bottom:.2rem;">Detallado</p>
                        <p style="font-size:.8rem; color:var(--color-text-secondary); line-height:1.4;">Incluye cada venta con todos sus productos, cantidades, precios unitarios y subtotales.</p>
                    </div>
                </button>

                {{-- Opción Resumen --}}
                <button wire:click="exportar('resumen')"
                        style="
                            display: flex; align-items: flex-start; gap: 1rem;
                            padding: 1rem 1.25rem;
                            border: 2px solid var(--color-celeste-dark);
                            border-radius: var(--radius-lg);
                            background: var(--color-celeste-muted);
                            cursor: pointer; text-align: left;
                            transition: all var(--transition-base);
                            width: 100%;
                        "
                        onmouseover="this.style.background='var(--color-celeste-light)'; this.style.transform='translateY(-1px)';"
                        onmouseout="this.style.background='var(--color-celeste-muted)'; this.style.transform='';">
                    <span style="font-size:1.5rem; flex-shrink:0;">📄</span>
                    <div>
                        <p style="font-weight:700; font-size:.9rem; color:var(--color-celeste-dark); margin-bottom:.2rem;">Resumen</p>
                        <p style="font-size:.8rem; color:var(--color-text-secondary); line-height:1.4;">Solo la lista de ventas con fecha, total de productos y precio final.</p>
                    </div>
                </button>
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button wire:click="$set('mostrarModalExport', false)" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Tabla --}}
    <div class="table-wrap animate-fade-in delay-100" style="margin-bottom:1.25rem;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Ítems</th>
                    <th class="text-center">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $v)
                <tr style="{{ $ventaDetalle === $v->id ? 'background:var(--color-turquesa-muted);' : '' }}">
                    <td style="font-family:monospace; font-size:.82rem; color:var(--color-celeste-dark);">#{{ $v->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</td>
                    <td class="text-right" style="font-weight:700; color:var(--color-turquesa);">S/ {{ number_format($v->total, 2) }}</td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ $v->detalles->count() }}</span>
                    </td>
                    <td class="text-center">
                        <button wire:click="verDetalle({{ $v->id }})" class="link-action">
                            {{ $ventaDetalle === $v->id ? '▲ Cerrar' : '▼ Ver' }}
                        </button>
                    </td>
                </tr>

                @if($ventaDetalle && $ventaDetalle === $v->id)
                <tr>
                    <td colspan="5" style="background:var(--color-surface-2); padding:1rem 1.5rem;">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; font-size:.75rem; color:var(--color-text-muted); font-weight:600; padding-bottom:.5rem; text-transform:uppercase; letter-spacing:.04em;">Producto</th>
                                    <th style="text-align:right; font-size:.75rem; color:var(--color-text-muted); font-weight:600; padding-bottom:.5rem; text-transform:uppercase; letter-spacing:.04em;">Precio</th>
                                    <th style="text-align:right; font-size:.75rem; color:var(--color-text-muted); font-weight:600; padding-bottom:.5rem; text-transform:uppercase; letter-spacing:.04em;">Cant.</th>
                                    <th style="text-align:right; font-size:.75rem; color:var(--color-text-muted); font-weight:600; padding-bottom:.5rem; text-transform:uppercase; letter-spacing:.04em;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventaAbierta->detalles as $d)
                                <tr style="border-top:1px solid var(--color-border);">
                                    <td style="padding:.5rem 0; font-weight:500;">{{ $d->producto->nombre ?? '—' }}</td>
                                    <td style="padding:.5rem 0; text-align:right;">S/ {{ number_format($d->precio_unitario, 2) }}</td>
                                    <td style="padding:.5rem 0; text-align:right;">{{ $d->cantidad }}</td>
                                    <td style="padding:.5rem 0; text-align:right; font-weight:700; color:var(--color-turquesa);">S/ {{ number_format($d->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endif

                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">Sin ventas en el período</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ventas->count())
    <div class="card animate-fade-in delay-200" style="padding:1rem 1.5rem; display:flex; gap:2rem; font-size:.875rem;">
        <div>
            <span style="color:var(--color-text-muted);">Ventas:</span>
            <strong style="margin-left:.35rem; color:var(--color-text-primary);">{{ $ventas->count() }}</strong>
        </div>
        <div>
            <span style="color:var(--color-text-muted);">Total período:</span>
            <strong style="margin-left:.35rem; color:var(--color-turquesa); font-size:1rem;">S/ {{ number_format($ventas->sum('total'), 2) }}</strong>
        </div>
    </div>
    @endif
</div>