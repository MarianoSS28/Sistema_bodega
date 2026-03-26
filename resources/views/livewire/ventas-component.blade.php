<div>
    <h1 class="text-xl font-bold mb-4">Nueva Venta</h1>

    @if(session('ok'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
    @endif

    {{-- Buscador --}}
    <div class="flex gap-2 mb-4">
        <input wire:model="codigoBusqueda"
               wire:keydown.enter="buscarProducto"
               placeholder="Escanea o escribe el código de barras..."
               class="border rounded px-3 py-2 flex-1"
               autofocus>
        <button wire:click="buscarProducto" class="bg-gray-700 text-white px-4 py-2 rounded">Buscar</button>
    </div>

    @if($error)
        <div class="bg-red-100 text-red-600 px-4 py-2 rounded mb-3">{{ $error }}</div>
    @endif

    {{-- Foto del último producto agregado --}}
    @if(count($carrito) && $carrito[array_key_last($carrito)]['foto_path'])
        <div class="mb-4 flex justify-center">
            <img src="{{ Storage::url($carrito[array_key_last($carrito)]['foto_path']) }}"
                 alt="Producto"
                 class="h-48 w-48 object-cover rounded shadow-lg border">
        </div>
    @endif

    {{-- Carrito --}}
    <table class="w-full bg-white rounded shadow text-sm mb-4">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 text-left">Producto</th>
                <th class="p-2 text-right">Precio</th>
                <th class="p-2 text-right">Cant.</th>
                <th class="p-2 text-right">Subtotal</th>
                <th class="p-2 text-center">—</th>
            </tr>
        </thead>
        <tbody>
            @forelse($carrito as $i => $item)
            <tr class="border-t">
                <td class="p-2 flex items-center gap-2">
                    @if($item['foto_path'])
                        <img src="{{ Storage::url($item['foto_path']) }}"
                             class="w-8 h-8 object-cover rounded">
                    @endif
                    {{ $item['nombre'] }}
                </td>
                <td class="p-2 text-right">S/ {{ number_format($item['precio_unitario'], 2) }}</td>
                <td class="p-2 text-right">{{ $item['cantidad'] }}</td>
                <td class="p-2 text-right">S/ {{ number_format($item['subtotal'], 2) }}</td>
                <td class="p-2 text-center">
                    <button wire:click="quitarItem({{ $i }})" class="text-red-500 hover:underline">✕</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-4 text-center text-gray-400">Carrito vacío</td></tr>
            @endforelse
        </tbody>
        @if(count($carrito))
        <tfoot>
            <tr class="border-t font-bold">
                <td colspan="3" class="p-2 text-right">TOTAL</td>
                <td class="p-2 text-right">S/ {{ number_format($total, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if(count($carrito))
    <div class="flex justify-end">
        <button wire:click="registrarVenta"
                wire:confirm="¿Confirmar venta por S/ {{ number_format($total, 2) }}?"
                class="bg-green-600 text-white px-6 py-2 rounded text-lg">
            Registrar Venta
        </button>
    </div>
    @endif
</div>