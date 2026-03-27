<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #0f2d1e; }
        h1 { font-size: 18px; color: #27B86D; margin-bottom: 2px; }
        .sub { font-size: 11px; color: #4a7360; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #27B86D; color: white; padding: 7px 8px; text-align: left; font-size: 11px; }
        th.right { text-align: right; }
        td { padding: 6px 8px; border-bottom: 1px solid #d1ede2; font-size: 11px; }
        td.right { text-align: right; }
        tr:nth-child(even) td { background: #f4fdf8; }
        .total-row td { font-weight: bold; background: #d4f5e5; border-top: 2px solid #27B86D; }
        .footer { margin-top: 16px; text-align: right; font-weight: bold; font-size: 13px; color: #27B86D; }
    </style>
</head>
<body>
    <h1>Historial de Ventas — Resumen</h1>
    <p class="sub">Período: {{ $fechaDesde }} al {{ $fechaHasta }} &nbsp;|&nbsp; Total ventas: {{ $ventas->count() }}</p>

    <table>
        <thead>
            <tr>
                <th># Venta</th>
                <th>Fecha</th>
                <th class="right">Total Prods.</th>
                <th class="right">Total (S/)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $v)
            <tr>
                <td>#{{ $v->id }}</td>
                <td>{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</td>
                <td class="right">{{ $v->detalles->sum('cantidad') }}</td>
                <td class="right">S/ {{ number_format($v->total, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">TOTAL PERÍODO</td>
                <td class="right">{{ $ventas->sum(fn($v) => $v->detalles->sum('cantidad')) }}</td>
                <td class="right">S/ {{ number_format($ventas->sum('total'), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p class="footer">Total período: S/ {{ number_format($ventas->sum('total'), 2) }}</p>
</body>
</html>