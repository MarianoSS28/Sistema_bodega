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
                    <th class="text-center">Color</th>
                    <th class="text-center">🧊 Helada</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Bloqueo</th>
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
                    <td style="color:var(--color-text-secondary); font-size:.85rem;">{{ $c->direccion }}</td>
                    <td class="text-center">
                        @if($c->color_primario)
                        <div style="display:inline-flex; align-items:center; gap:.4rem;">
                            <div style="width:20px; height:20px; border-radius:50%;
                                        background:{{ $c->color_primario }};
                                        border:2px solid var(--color-border);
                                        box-shadow:0 1px 4px rgba(0,0,0,.15);">
                            </div>
                            <span style="font-family:monospace; font-size:.72rem; color:var(--color-text-muted);">
                                {{ $c->color_primario }}
                            </span>
                        </div>
                        @else
                        <span style="color:var(--color-text-muted); font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(($c->precio_helada ?? 0) > 0)
                            <span class="badge badge-info">+S/ {{ number_format($c->precio_helada, 2) }}</span>
                        @else
                            <span style="color:var(--color-text-muted); font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($c->estado)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if((int)($c->bloqueado ?? 0))
                            <span class="badge badge-danger">🔒 Bloqueado</span>
                        @else
                            <span class="badge" style="background:var(--color-surface-2); color:var(--color-text-muted);">Libre</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-size:.8rem; color:var(--color-text-muted);">
                        {{ $c->yape_qr ? 'Yape' : '' }}
                        {{ ($c->yape_qr && $c->plin_qr) ? '·' : '' }}
                        {{ $c->plin_qr ? 'Plin' : '' }}
                        @if(!$c->yape_qr && !$c->plin_qr) — @endif
                    </td>
                    <td class="text-center">
                        <div style="display:flex; gap:.4rem; justify-content:center; flex-wrap:wrap;">
                            <button wire:click="abrirFormulario({{ $c->id }})" class="link-action">Editar</button>

                            <button wire:click="toggleBloqueo({{ $c->id }})"
                                    wire:confirm="{{ (int)($c->bloqueado ?? 0) ? '¿Desbloquear ' . $c->nombre . '?' : '¿Bloquear ' . $c->nombre . '? Los usuarios no podrán acceder.' }}"
                                    class="link-action {{ (int)($c->bloqueado ?? 0) ? '' : 'danger' }}">
                                {{ (int)($c->bloqueado ?? 0) ? '🔓 Desbloquear' : '🔒 Bloquear' }}
                            </button>

                            @if($c->estado)
                            <button wire:click="desactivar({{ $c->id }})"
                                    wire:confirm="¿Desactivar {{ $c->nombre }}?"
                                    class="link-action danger">Desactivar</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
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
    @teleport('body')
        <div class="modal-backdrop">
            <div class="modal-box animate-scale-in" style="max-width:600px; max-height:92vh; overflow-y:auto;">
                <h2 class="modal-title">{{ $editandoId ? 'Editar' : 'Nuevo' }} Comercio</h2>

                <div style="display:flex; flex-direction:column; gap:.85rem;">

                    {{-- Nombre y dirección --}}
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

                    {{-- Estado --}}
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Estado</label>
                        <select wire:model="estado" class="input" style="width:auto;">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    {{-- Color primario y precio helada --}}
                    <div style="display:flex; gap:1rem; align-items:flex-end; padding:.85rem 1rem;
                                background:var(--color-surface-2); border-radius:var(--radius-md);
                                border:1px solid var(--color-border);">
                        <div>
                            <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">
                                🎨 Color del sistema
                            </label>
                            <div style="display:flex; align-items:center; gap:.5rem;">
                                <input wire:model.live="color_primario" type="color"
                                       style="width:48px; height:40px; padding:2px; border-radius:var(--radius-md);
                                              border:1.5px solid var(--color-border); cursor:pointer;">
                                <span style="font-family:monospace; font-size:.78rem; color:var(--color-text-muted);">
                                    {{ $color_primario }}
                                </span>
                                <div style="width:28px; height:28px; border-radius:50%;
                                            background:{{ $color_primario }};
                                            border:2px solid var(--color-border);
                                            box-shadow:0 2px 6px rgba(0,0,0,.12);">
                                </div>
                            </div>
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">
                                🧊 Precio adicional helada (S/)
                            </label>
                            <input wire:model="precio_helada" type="number" step="0.50" min="0"
                                   class="input" placeholder="0.00">
                            @error('precio_helada') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                        </div>
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

                    {{-- QR --}}
                    <div style="display:flex; gap:1rem;">
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

                    {{-- Bloqueo --}}
                    <div style="border-radius:var(--radius-md); padding:.85rem 1rem;
                                background:{{ $bloqueado ? 'var(--color-danger-light)' : 'var(--color-surface-2)' }};
                                border:1.5px solid {{ $bloqueado ? 'var(--color-danger)' : 'var(--color-border)' }};
                                transition:all var(--transition-base);">
                        <label style="display:flex; align-items:center; gap:.6rem; cursor:pointer;">
                            <input type="checkbox" wire:model.live="bloqueado" value="1"
                                   style="width:16px; height:16px; accent-color:var(--color-danger);">
                            <span style="font-size:.875rem; font-weight:600; color:{{ $bloqueado ? 'var(--color-danger)' : 'var(--color-text-secondary)' }};">
                                🔒 Bloquear acceso a este comercio
                            </span>
                        </label>
                        @if($bloqueado)
                        <div class="animate-fade-in" style="margin-top:.65rem;">
                            <label style="display:block; font-size:.78rem; font-weight:600; color:var(--color-danger); margin-bottom:.3rem;">
                                Motivo del bloqueo (se mostrará a todos los usuarios del comercio)
                            </label>
                            <textarea wire:model="motivo_bloqueo"
                                      class="input"
                                      rows="2"
                                      placeholder="Ej: Comercio suspendido por falta de pago."
                                      style="resize:none; border-color:var(--color-danger);"></textarea>
                        </div>
                        @endif
                    </div>

                </div>

                <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                    <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                    <button wire:click="guardar" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    @endteleport
    @endif
</div>