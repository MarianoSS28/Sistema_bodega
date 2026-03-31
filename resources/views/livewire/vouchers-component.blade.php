<div class="animate-fade-in">
    <h1 class="page-title">Vouchers</h1>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">✅ {{ session('ok') }}</div>
    @endif

    {{-- Visor de foto ampliada --}}
    @if($fotoAmpliada)
    @teleport('body')
    <div class="modal-backdrop" style="z-index:500; cursor:zoom-out;" wire:click="cerrarFoto">
        <div style="position:relative; max-width:90vw; max-height:90vh;" onclick="event.stopPropagation()">
            <img src="{{ Storage::url($fotoAmpliada) }}"
                 style="max-width:90vw; max-height:85vh; object-fit:contain; border-radius:var(--radius-lg); box-shadow:var(--shadow-lg);">
            <button wire:click="cerrarFoto"
                    style="
                        position:absolute; top:-14px; right:-14px;
                        width:36px; height:36px;
                        background:var(--color-danger);
                        color:#fff;
                        border:none; border-radius:50%;
                        font-size:1rem; cursor:pointer;
                        display:flex; align-items:center; justify-content:center;
                        box-shadow:var(--shadow-md);
                    ">✕</button>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Modal de edición --}}
    @if($editandoId)
    @teleport('body')
    <div class="modal-backdrop" style="z-index:100;">
        <div class="modal-box animate-scale-in">
            <h2 class="modal-title">✏️ Editar Voucher</h2>

            <div style="display:flex; flex-direction:column; gap:.85rem;">
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">ID de Venta</label>
                    <input wire:model="editIdVenta" type="number" placeholder="Ej: 1" class="input">
                    @error('editIdVenta') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Nueva foto (opcional)</label>
                    @if($editFoto)
                        <img src="{{ $editFoto->temporaryUrl() }}"
                             style="width:120px; border-radius:var(--radius-md); margin-bottom:.5rem; border:2px solid var(--color-turquesa);">
                    @endif
                    <input type="file" wire:model="editFoto" accept="image/*" class="input">
                    @error('editFoto') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    <p style="font-size:.75rem; color:var(--color-text-muted); margin-top:.3rem;">Deja vacío para conservar la foto actual.</p>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                <button wire:click="cancelarEdicion" class="btn btn-secondary">Cancelar</button>
                <button wire:click="guardarEdicion"  class="btn btn-primary">Guardar cambios</button>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Formulario subir voucher --}}
    <div class="card animate-fade-in delay-50" style="padding:1.5rem; margin-bottom:1.5rem; max-width:440px;">
        <div style="display:flex; align-items:center; gap:.5rem; margin-bottom:1rem;">
            <span style="font-size:1.1rem;">📎</span>
            <p style="font-weight:700; font-size:.95rem; color:var(--color-text-primary);">Subir nuevo voucher</p>
        </div>

        <div style="
            background: var(--color-celeste-muted);
            border: 1px solid var(--color-celeste-dark);
            border-radius: var(--radius-md);
            padding: .6rem .9rem;
            font-size: .78rem;
            color: var(--color-celeste-dark);
            margin-bottom: 1rem;
            display: flex; align-items: center; gap: .4rem;
        ">
            ℹ️ Solo se permite <strong>1 voucher por venta</strong>. Para actualizar uno existente usa "Editar".
        </div>

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
                📤 Subir Voucher
            </button>
        </div>
    </div>

    {{-- Grid vouchers --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:1rem;">
        @forelse($vouchers as $v)
        <div class="card animate-fade-in" style="overflow:hidden;">
            {{-- Foto clicable --}}
            <div style="position:relative; cursor:zoom-in;" wire:click="verFoto('{{ $v->foto_path }}')">
                <img src="{{ Storage::url($v->foto_path) }}"
                     style="width:100%; height:150px; object-fit:cover; display:block; transition: transform var(--transition-base);"
                     onmouseover="this.style.transform='scale(1.03)'"
                     onmouseout="this.style.transform=''">
                <div style="
                    position:absolute; inset:0;
                    background:rgba(0,0,0,0);
                    display:flex; align-items:center; justify-content:center;
                    transition: background var(--transition-base);
                    border-radius:0;
                "
                onmouseover="this.style.background='rgba(0,0,0,.25)'; this.querySelector('span').style.opacity='1';"
                onmouseout="this.style.background='rgba(0,0,0,0)'; this.querySelector('span').style.opacity='0';">
                    <span style="color:#fff; font-size:1.5rem; opacity:0; transition: opacity var(--transition-fast);">🔍</span>
                </div>
            </div>

            <div style="padding:.75rem 1rem;">
                {{-- Info --}}
                <p style="font-size:.875rem; font-weight:700; color:var(--color-text-primary); margin-bottom:.2rem;">
                    Venta <span style="color:var(--color-celeste-dark);">#{{ $v->id_venta }}</span>
                </p>
                <p style="font-size:.82rem; color:var(--color-turquesa); font-weight:600; margin-bottom:.2rem;">S/ {{ number_format($v->total, 2) }}</p>
                <p style="font-size:.75rem; color:var(--color-text-muted); margin-bottom:.75rem;">{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</p>

                {{-- Acciones --}}
                <div style="display:flex; gap:.5rem;">
                    <button wire:click="verFoto('{{ $v->foto_path }}')"
                            style="flex:1; padding:.35rem; background:var(--color-celeste-muted); border:1.5px solid var(--color-celeste-dark); border-radius:var(--radius-sm); font-size:.75rem; color:var(--color-celeste-dark); font-weight:600; cursor:pointer; transition:all var(--transition-fast);"
                            onmouseover="this.style.background='var(--color-celeste-dark)'; this.style.color='#fff';"
                            onmouseout="this.style.background='var(--color-celeste-muted)'; this.style.color='var(--color-celeste-dark)';">
                        🔍 Ver
                    </button>
                    <button wire:click="abrirEdicion({{ $v->id }})"
                            style="flex:1; padding:.35rem; background:var(--color-turquesa-muted); border:1.5px solid var(--color-turquesa); border-radius:var(--radius-sm); font-size:.75rem; color:var(--color-turquesa-dark); font-weight:600; cursor:pointer; transition:all var(--transition-fast);"
                            onmouseover="this.style.background='var(--color-turquesa)'; this.style.color='#fff';"
                            onmouseout="this.style.background='var(--color-turquesa-muted)'; this.style.color='var(--color-turquesa-dark)';">
                        ✏️ Editar
                    </button>
                </div>
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