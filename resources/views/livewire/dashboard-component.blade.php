<div wire:poll.15s="cargar">
    <h1 class="page-title animate-fade-in">
        Dashboard
    </h1>

    @if($resumen)
    {{-- Stat Cards --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-bottom:1.75rem;">

        <div class="stat-card animate-fade-in delay-50">
            <div class="accent-bar" style="background: var(--gradient-brand);"></div>
            <div class="icon-wrap" style="background:var(--color-turquesa-light); color:var(--color-turquesa);">📦</div>
            <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.2rem;">Ventas hoy</p>
            <p style="font-size:2rem; font-weight:800; color:var(--color-text-primary); line-height:1;">{{ $resumen->ventas_hoy }}</p>
            <p style="font-size:.9rem; font-weight:700; color:var(--color-turquesa); margin-top:.25rem;">S/ {{ number_format($resumen->total_hoy, 2) }}</p>
        </div>

        <div class="stat-card animate-fade-in delay-100">
            <div class="accent-bar" style="background: linear-gradient(90deg, var(--color-verde), var(--color-celeste-dark));"></div>
            <div class="icon-wrap" style="background:var(--color-verde-light); color:var(--color-verde-dark);">📊</div>
            <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.2rem;">Ventas del mes</p>
            <p style="font-size:2rem; font-weight:800; color:var(--color-text-primary); line-height:1;">{{ $resumen->ventas_mes }}</p>
            <p style="font-size:.9rem; font-weight:700; color:var(--color-verde-dark); margin-top:.25rem;">S/ {{ number_format($resumen->total_mes, 2) }}</p>
        </div>

        <div class="stat-card animate-fade-in delay-150">
            <div class="accent-bar" style="background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>
            <div class="icon-wrap" style="background:var(--color-warning-light); color:var(--color-warning);">⚠️</div>
            <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.2rem;">Por acabar</p>
            <p style="font-size:2rem; font-weight:800; color:var(--color-warning); line-height:1;">{{ $resumen->productos_por_acabar }}</p>
            <p style="font-size:.8rem; color:var(--color-text-muted); margin-top:.25rem;">productos ≤ 5 unidades</p>
        </div>

        <div class="stat-card animate-fade-in delay-200">
            <div class="accent-bar" style="background: linear-gradient(90deg, var(--color-danger), #f97316);"></div>
            <div class="icon-wrap" style="background:var(--color-danger-light); color:var(--color-danger);">🚫</div>
            <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.2rem;">Agotados</p>
            <p style="font-size:2rem; font-weight:800; color:var(--color-danger); line-height:1;">{{ $resumen->productos_agotados }}</p>
            <p style="font-size:.8rem; color:var(--color-text-muted); margin-top:.25rem;">sin stock</p>
        </div>
    </div>

    {{-- Tabla ventas últimos 7 días --}}
    <div class="table-wrap animate-fade-in delay-300">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--color-border); display:flex; align-items:center; gap:.5rem;">
            <span style="font-size:1rem; font-weight:700; color:var(--color-text-primary);">Ventas últimos 7 días</span>
            <span class="badge badge-info" style="margin-left:auto;">Actualiza cada 15s</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th class="text-right">Ventas</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventasDias as $dia)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                    <td class="text-right">
                        <span class="badge badge-info">{{ $dia->cantidad_ventas }}</span>
                    </td>
                    <td class="text-right" style="font-weight:700; color:var(--color-turquesa);">
                        S/ {{ number_format($dia->total_ventas, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center; padding:2rem; color:var(--color-text-muted);">Sin datos</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background:var(--color-turquesa-muted); border-top:2px solid var(--color-border);">
                    <td style="font-weight:700; padding:.75rem 1rem;">Total</td>
                    <td class="text-right" style="font-weight:700; padding:.75rem 1rem;">{{ collect($ventasDias)->sum('cantidad_ventas') }}</td>
                    <td class="text-right" style="font-weight:800; color:var(--color-turquesa); padding:.75rem 1rem;">S/ {{ number_format(collect($ventasDias)->sum('total_ventas'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>