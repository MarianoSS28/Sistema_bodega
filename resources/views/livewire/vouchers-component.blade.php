<div class="animate-fade-in">
    <h1 class="page-title">Vouchers</h1>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">✅ {{ session('ok') }}</div>
    @endif

    {{-- Formulario --}}
    <div class="card animate-fade-in delay-50" style="padding:1.5rem; margin-bottom:1.5rem; max-width:420px;">
        <p style="font-weight:700; font-size:.95rem; color:var(--color-text-primary); margin-bottom:1rem;">📎 Subir nuevo voucher</p>

        <div style="display:flex; flex-direction:column; gap:.85rem;">
            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">ID de Venta</label>
                <input wire:model="idVenta" type="number" placeholder="Ej: 1" class="input">
                @error('idVenta') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
            </div>
            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Foto del voucher</label>
                @if($foto)
                    <img src="{{ $foto->temporaryUrl() }}"
                         style="width:120px; border-radius:var(--radius-md); margin-bottom:.5rem; border:2px solid var(--color-turquesa);">
                @endif
                <input type="file" wire:model="foto" accept="image/*" class="input">
                @error('foto') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
            </div>
            <button wire:click="subirVoucher" class="btn btn-primary" style="width:100%; justify-content:center;">
                Subir Voucher
            </button>
        </div>
    </div>

    {{-- Grid vouchers --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem;">
        @forelse($vouchers as $v)
        <div class="card animate-fade-in" style="overflow:hidden; transition: transform var(--transition-spring), box-shadow var(--transition-base);"
             onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-md)'"
             onmouseout="this.style.transform=''; this.style.boxShadow=''">
            <img src="{{ Storage::url($v->foto_path) }}"
                 style="width:100%; height:140px; object-fit:cover;">
            <div style="padding:.75rem 1rem;">
                <p style="font-size:.875rem; font-weight:700; color:var(--color-text-primary);">Venta #{{ $v->id_venta }}</p>
                <p style="font-size:.82rem; color:var(--color-turquesa); font-weight:600;">S/ {{ number_format($v->total, 2) }}</p>
                <p style="font-size:.75rem; color:var(--color-text-muted);">{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        @empty
        <p style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--color-text-muted);">
            <span style="font-size:2rem; display:block; margin-bottom:.5rem;">🧾</span>
            No hay vouchers registrados.
        </p>
        @endforelse
    </div>
</div>