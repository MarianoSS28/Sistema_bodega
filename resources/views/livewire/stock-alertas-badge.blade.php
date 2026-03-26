<div class="relative" wire:poll.30s="cargar">
    <button wire:click="toggleMostrar" class="relative flex items-center gap-1 text-white hover:text-yellow-400">
        🔔
        @if($totalAlertas > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ $totalAlertas }}
            </span>
        @endif
    </button>

    @if($mostrar && $totalAlertas > 0)
    <div class="absolute right-0 top-8 bg-white text-gray-800 rounded shadow-lg w-72 z-50 border">
        <div class="px-4 py-2 border-b font-semibold text-sm flex justify-between">
            <span>Alertas de stock</span>
            <span class="text-red-600">{{ $agotados }} agotados</span>
        </div>
        <ul class="max-h-64 overflow-y-auto divide-y">
            @foreach($items as $item)
            <li class="px-4 py-2 text-sm flex justify-between items-center">
                <span>{{ $item->nombre }}</span>
                <span class="{{ $item->tipo_alerta === 'AGOTADO' ? 'text-red-600 font-bold' : 'text-orange-500 font-semibold' }}">
                    {{ $item->tipo_alerta === 'AGOTADO' ? 'AGOTADO' : "Stock: {$item->stock}" }}
                </span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>