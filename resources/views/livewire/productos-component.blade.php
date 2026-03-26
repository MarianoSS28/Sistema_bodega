<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Productos</h1>
        <button wire:click="abrirFormulario()" class="bg-blue-600 text-white px-4 py-2 rounded">+ Nuevo</button>
    </div>

    @if(session('ok'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
    @endif

    <input wire:model.live="busqueda" placeholder="Buscar por nombre o código..."
           class="border rounded px-3 py-2 w-full mb-4">

    <table class="w-full bg-white rounded shadow text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 text-left">Código</th>
                <th class="p-2 text-left">Nombre</th>
                <th class="p-2 text-right">Precio</th>
                <th class="p-2 text-right">Stock</th>
                <th class="p-2 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $p)
            <tr class="border-t">
                <td class="p-2">{{ $p->codigo_barras }}</td>
                <td class="p-2">{{ $p->nombre }}</td>
                <td class="p-2 text-right">S/ {{ number_format($p->precio, 2) }}</td>
                <td class="p-2 text-right">{{ $p->stock }}</td>
                <td class="p-2 text-center space-x-2">
                    <button wire:click="abrirFormulario({{ $p->id }})" class="text-blue-600 hover:underline">Editar</button>
                    <button wire:click="desactivar({{ $p->id }})"
                            wire:confirm="¿Desactivar este producto?"
                            class="text-red-600 hover:underline">Eliminar</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-4 text-center text-gray-400">Sin resultados</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Modal formulario --}}
    @if($mostrarFormulario)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded shadow p-6 w-full max-w-md">
            <h2 class="text-lg font-bold mb-4">{{ $editandoId ? 'Editar' : 'Nuevo' }} Producto</h2>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium">Nombre</label>
                    <input wire:model="nombre" class="border rounded w-full px-3 py-2">
                    @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Código de barras</label>
                    <input wire:model="codigo_barras" class="border rounded w-full px-3 py-2">
                    @error('codigo_barras') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium">Precio</label>
                        <input wire:model="precio" type="number" step="0.01" class="border rounded w-full px-3 py-2">
                        @error('precio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium">Stock</label>
                        <input wire:model="stock" type="number" class="border rounded w-full px-3 py-2">
                        @error('stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-5">
                <button wire:click="$set('mostrarFormulario', false)" class="px-4 py-2 border rounded">Cancelar</button>
                <button wire:click="guardar" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
            </div>
        </div>
    </div>
    @endif
</div>