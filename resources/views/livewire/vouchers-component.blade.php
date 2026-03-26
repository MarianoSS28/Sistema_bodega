<div>
    <h1 class="text-xl font-bold mb-4">Vouchers</h1>

    @if(session('ok'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
    @endif

    {{-- Formulario subir voucher --}}
    <div class="bg-white rounded shadow p-4 mb-6 max-w-md">
        <h2 class="font-semibold mb-3">Subir nuevo voucher</h2>
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium">ID de Venta</label>
                <input wire:model="idVenta" type="number" placeholder="Ej: 1"
                       class="border rounded w-full px-3 py-2">
                @error('idVenta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Foto del voucher</label>
                @if($foto)
                    <img src="{{ $foto->temporaryUrl() }}" class="w-40 rounded mb-2 border">
                @endif
                <input type="file" wire:model="foto" accept="image/*" class="border rounded w-full px-3 py-2">
                @error('foto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button wire:click="subirVoucher" class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                Subir Voucher
            </button>
        </div>
    </div>

    {{-- Listado de vouchers --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($vouchers as $v)
        <div class="bg-white rounded shadow p-3 text-center">
            <img src="{{ Storage::url($v->foto_path) }}" class="w-full h-40 object-cover rounded mb-2">
            <p class="text-sm font-semibold">Venta #{{ $v->id_venta }}</p>
            <p class="text-xs text-gray-500">S/ {{ number_format($v->total, 2) }}</p>
            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</p>
        </div>
        @empty
        <p class="col-span-4 text-center text-gray-400 py-8">No hay vouchers registrados.</p>
        @endforelse
    </div>
</div>