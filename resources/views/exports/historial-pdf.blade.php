<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #0f2d1e; }
        h1 { font-size: 18px; color: #27B86D; margin-bottom: 2px; }
        .sub { font-size: 11px; color: #4a7360; margin-bottom: 14px; }
        .venta-header { background: #27B86D; color: #fff; padding: 6px 10px; border-radius: 4px; margin-top: 14px; margin-bottom: 0; font-size: 12px; display: flex; }
        .venta-header span { margin-right: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        thead th { background: #d4f5e5; color: #0f2d1e; padding: 5px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: .04em; }
        thead th.right { text-align: right; }
        td { padding: 5px 8px; border-bottom: 1px solid #d1ede2; font-size: 11px; }
        td.right { text-align: right; }
        .subtotal-row td { font-weight: bold; background: #e8faf2; border-top: 1px solid #43AA72; }
        .grand-total { margin-top: 16px; text-align: right; font-weight: bold; font-size: 14px; color: #27B86D; border-top: 2px solid #27B86D; padding-top: 8px; }
    </style>
</head>
<body>
    <h1>Historial de Ventas — Detallado</h1>
    <p class="sub">Período: {{ $fechaDesde }} al {{ $fechaHasta }} &nbsp;|&nbsp; Total ventas: {{ $ventas->count() }}</p>

    @foreach($ventas as $v)
    <div class="venta-header">
        <span><strong>Venta #{{ $v->id }}</strong></span>
        <span>{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</span>
        <span style="margin-left:auto;"><strong>Total: S/ {{ number_format($v->total, 2) }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="right">Precio Unit.</th>
                <th class="right">Cantidad</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($v->detalles as $d)
            <tr>
                <td>{{ $d->producto->nombre ?? '—' }}</td>
                <td class="right">S/ {{ number_format($d->precio_unitario, 2) }}</td>
                <td class="right">{{ $d->cantidad }}</td>
                <td class="right">S/ {{ number_format($d->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="3" style="text-align:right;">Subtotal venta</td>
                <td class="right">S/ {{ number_format($v->total, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <p class="grand-total">TOTAL PERÍODO: S/ {{ number_format($ventas->sum('total'), 2) }}</p>
</body>
</html>