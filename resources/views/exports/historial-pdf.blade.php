<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #333; color: white; padding: 6px; text-align: left; }
        td { padding: 5px; border-bottom: 1px solid #ddd; }
        .total { font-weight: bold; text-align: right; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Historial de Ventas</h1>
    <p>Período: {{ $fechaDesde }} al {{ $fechaHasta }}</p>

    <table>
        <thead>
            <tr>
                <th># Venta</th>
                <th>Fecha</th>
                <th>Ítems</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $v)
            <tr>
                <td>{{ $v->id }}</td>
                <td>{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</td>
                <td>
                    @foreach($v->detalles as $d)
                        {{ $d->producto->nombre ?? '—' }} x{{ $d->cantidad }} (S/ {{ number_format($d->subtotal,2) }})<br>
                    @endforeach
                </td>
                <td>S/ {{ number_format($v->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total período: S/ {{ number_format($ventas->sum('total'), 2) }}</p>
</body>
</html>