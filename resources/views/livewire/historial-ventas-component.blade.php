<div>
    <h1 class="text-xl font-bold mb-4">Historial de Ventas</h1>

    {{-- Filtros --}}
    <div class="flex gap-4 mb-4">
        <div>
            <label class="text-sm font-medium">Desde</label>
            <input wire:model.live="fechaDesde" type="date" class="border rounded px-3 py-2 block">
        </div>
        <div>
            <label class="text-sm font-medium">Hasta</label>
            <input wire:model.live="fechaHasta" type="date" class="border rounded px-3 py-2 block">
        </div>
    </div>

    {{-- Tabla ventas --}}
    <table class="w-full bg-white rounded shadow text-sm mb-6">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 text-left">#</th>
                <th class="p-2 text-left">Fecha</th>
                <th class="p-2 text-right">Total</th>
                <th class="p-2 text-center">Ítems</th>
                <th class="p-2 text-center">Detalle</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $v)
            <tr class="border-t {{ $ventaDetalle === $v->id ? 'bg-blue-50' : '' }}">
                <td class="p-2 font-mono">{{ $v->id }}</td>
                <td class="p-2">{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</td>
                <td class="p-2 text-right font-semibold">S/ {{ number_format($v->total, 2) }}</td>
                <td class="p-2 text-center">{{ $v->detalles->count() }}</td>
                <td class="p-2 text-center">
                    <button wire:click="verDetalle({{ $v->id }})" class="text-blue-600 hover:underline">
                        {{ $ventaDetalle === $v->id ? 'Cerrar' : 'Ver' }}
                    </button>
                </td>
            </tr>

            {{-- Detalle inline --}}
            @if($ventaDetalle && $ventaDetalle === $v->id)
            <tr>
                <td colspan="5" class="bg-gray-50 px-4 py-3">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-gray-500">
                                <th class="text-left pb-1">Producto</th>
                                <th class="text-right pb-1">Precio</th>
                                <th class="text-right pb-1">Cant.</th>
                                <th class="text-right pb-1">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventaAbierta->detalles as $d)
                            <tr class="border-t border-gray-200">
                                <td class="py-1">{{ $d->producto->nombre ?? '—' }}</td>
                                <td class="py-1 text-right">S/ {{ number_format($d->precio_unitario, 2) }}</td>
                                <td class="py-1 text-right">{{ $d->cantidad }}</td>
                                <td class="py-1 text-right">S/ {{ number_format($d->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif

            @empty
            <tr><td colspan="5" class="p-4 text-center text-gray-400">Sin ventas en el período</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Resumen --}}
    @if($ventas->count())
    <div class="bg-white rounded shadow p-4 flex gap-8 text-sm">
        <div><span class="text-gray-500">Ventas:</span> <strong>{{ $ventas->count() }}</strong></div>
        <div><span class="text-gray-500">Total período:</span> <strong>S/ {{ number_format($ventas->sum('total'), 2) }}</strong></div>
    </div>
    @endif
</div>