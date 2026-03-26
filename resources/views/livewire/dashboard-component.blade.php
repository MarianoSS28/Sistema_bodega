<div wire:poll.60s="cargar">
    <h1 class="text-xl font-bold mb-6">Dashboard</h1>

    @if($resumen)
    {{-- Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Ventas hoy</p>
            <p class="text-2xl font-bold">{{ $resumen->ventas_hoy }}</p>
            <p class="text-sm text-blue-600 font-semibold">S/ {{ number_format($resumen->total_hoy, 2) }}</p>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Ventas del mes</p>
            <p class="text-2xl font-bold">{{ $resumen->ventas_mes }}</p>
            <p class="text-sm text-green-600 font-semibold">S/ {{ number_format($resumen->total_mes, 2) }}</p>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-orange-500">
            <p class="text-sm text-gray-500">Stock por acabar</p>
            <p class="text-2xl font-bold text-orange-500">{{ $resumen->productos_por_acabar }}</p>
            <p class="text-sm text-gray-400">productos ≤ 5 unidades</p>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Productos agotados</p>
            <p class="text-2xl font-bold text-red-600">{{ $resumen->productos_agotados }}</p>
            <p class="text-sm text-gray-400">sin stock</p>
        </div>
    </div>

    {{-- Tabla ventas últimos 7 días --}}
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Ventas últimos 7 días</h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Fecha</th>
                    <th class="p-2 text-right">Cantidad ventas</th>
                    <th class="p-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventasDias as $dia)
                <tr class="border-t">
                    <td class="p-2">{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                    <td class="p-2 text-right">{{ $dia->cantidad_ventas }}</td>
                    <td class="p-2 text-right font-semibold">S/ {{ number_format($dia->total_ventas, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="p-4 text-center text-gray-400">Sin datos</td></tr>
                @endforelse
            </tbody>
            <tfoot class="border-t font-bold">
                <tr>
                    <td class="p-2">Total</td>
                    <td class="p-2 text-right">{{ collect($ventasDias)->sum('cantidad_ventas') }}</td>
                    <td class="p-2 text-right">S/ {{ number_format(collect($ventasDias)->sum('total_ventas'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>