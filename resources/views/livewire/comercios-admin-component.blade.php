<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">🏪 Comercios</h1>
        <button wire:click="abrirFormulario()" class="btn btn-primary">+ Nuevo Comercio</button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; {{ session('ok') }}</div>
    @endif

    <input wire:model.live="busqueda"
           placeholder="Buscar por nombre o dirección..."
           class="input"
           style="margin-bottom:1rem; max-width:380px;">

    <div class="table-wrap animate-fade-in delay-100">
        <table>
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">QR</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comercios as $c)
                <tr>
                    <td>
                        @if($c->logo_path)
                            <img src="{{ Storage::url($c->logo_path) }}"
                                 style="width:40px; height:40px; object-fit:cover; border-radius:var(--radius-md);">
                        @else
                            <div style="width:40px; height:40px; background:var(--color-turquesa-muted);
                                        border-radius:var(--radius-md); display:flex; align-items:center;
                                        justify-content:center; font-size:.7rem; color:var(--color-text-muted);">
                                sin logo
                            </div>
                        @endif
                    </td>
                    <td style="font-weight:600;">{{ $c->nombre }}</td>
                    <td style="color:var(--color-text-secondary);">{{ $c->direccion }}</td>
                    <td class="text-center">
                        @if($c->estado)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-size:.8rem; color:var(--color-text-muted);">
                        {{ $c->yape_qr ? 'Yape' : '' }}
                        {{ ($c->yape_qr && $c->plin_qr) ? '·' : '' }}
                        {{ $c->plin_qr ? 'Plin' : '' }}
                        @if(!$c->yape_qr && !$c->plin_qr) — @endif
                    </td>
                    <td class="text-center">
                        <div style="display:flex; gap:.5rem; justify-content:center;">
                            <button wire:click="abrirFormulario({{ $c->id }})" class="link-action">Editar</button>
                            @if($c->estado)
                            <button wire:click="desactivar({{ $c->id }})"
                                    wire:confirm="¿Desactivar este comercio?"
                                    class="link-action danger">Desactivar</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
                        Sin resultados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($comercios->hasPages())
        <div style="padding:.85rem 1.25rem; border-top:1px solid var(--color-border);">
            {{ $comercios->links() }}
        </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($mostrarFormulario)
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in" style="max-width:560px;">
            <h2 class="modal-title">{{ $editandoId ? 'Editar' : 'Nuevo' }} Comercio</h2>

            <div style="display:flex; flex-direction:column; gap:.85rem;">

                <div style="display:flex; gap:.75rem;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Nombre</label>
                        <input wire:model="nombre" class="input" placeholder="Nombre del comercio">
                        @error('nombre') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Dirección</label>
                        <input wire:model="direccion" class="input" placeholder="Dirección">
                        @error('direccion') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Estado</label>
                    <select wire:model="estado" class="input" style="width:auto;">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

                {{-- Logo --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Logo</label>
                    <div style="display:flex; align-items:center; gap:1rem;">
                        @if($logo)
                            <img src="{{ $logo->temporaryUrl() }}"
                                 style="height:56px; border-radius:var(--radius-md); border:2px solid var(--color-turquesa);">
                        @elseif($logo_path)
                            <img src="{{ Storage::url($logo_path) }}"
                                 style="height:56px; border-radius:var(--radius-md); border:2px solid var(--color-border);">
                        @endif
                        <input type="file" wire:model="logo" accept="image/*" class="input">
                    </div>
                    @error('logo') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display:flex; gap:1rem;">
                    {{-- Yape QR --}}
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">QR Yape</label>
                        @if($yape_qr)
                            <img src="{{ $yape_qr->temporaryUrl() }}"
                                 style="height:80px; border-radius:var(--radius-md); border:2px solid var(--color-turquesa); display:block; margin-bottom:.4rem;">
                        @elseif($yape_qr_path)
                            <img src="{{ Storage::url($yape_qr_path) }}"
                                 style="height:80px; border-radius:var(--radius-md); border:2px solid var(--color-border); display:block; margin-bottom:.4rem;">
                        @endif
                        <input type="file" wire:model="yape_qr" accept="image/*" class="input">
                        @error('yape_qr') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Plin QR --}}
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">QR Plin</label>
                        @if($plin_qr)
                            <img src="{{ $plin_qr->temporaryUrl() }}"
                                 style="height:80px; border-radius:var(--radius-md); border:2px solid #7c3aed; display:block; margin-bottom:.4rem;">
                        @elseif($plin_qr_path)
                            <img src="{{ Storage::url($plin_qr_path) }}"
                                 style="height:80px; border-radius:var(--radius-md); border:2px solid var(--color-border); display:block; margin-bottom:.4rem;">
                        @endif
                        <input type="file" wire:model="plin_qr" accept="image/*" class="input">
                        @error('plin_qr') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="guardar" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
    @endif
</div>