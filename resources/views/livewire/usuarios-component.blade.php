<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">Usuarios</h1>
        <button wire:click="abrirFormulario()" class="btn btn-primary">+ Nuevo Usuario</button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; {{ session('ok') }}</div>
    @endif

    <input wire:model.live="busqueda"
           placeholder="Buscar por nombre o DNI..."
           class="input"
           style="margin-bottom:1rem; max-width:380px;">

    <div class="table-wrap animate-fade-in delay-100">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Rol</th>
                    <th>Comercio</th>
                    <th>Menús</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                <tr>
                    <td style="font-weight:500;">{{ $u->nombre_completo }}</td>
                    <td style="font-family:monospace; color:var(--color-celeste-dark);">{{ $u->dni }}</td>
                    <td><span class="badge badge-info">{{ $u->rol->nombre ?? '—' }}</span></td>
                    <td style="font-size:.82rem; color:var(--color-text-secondary);">
                        {{ $u->comercio->nombre ?? '—' }}
                    </td>
                    <td style="font-size:.8rem; color:var(--color-text-muted);">
                        {{ $u->menus()->count() }} menú(s)
                    </td>
                    <td class="text-center">
                        @if((int)($u->bloqueado ?? 0))
                            <span class="badge badge-danger">🔒 Bloqueado</span>
                        @else
                            <span class="badge badge-success">✓ Activo</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div style="display:flex; gap:.4rem; justify-content:center; flex-wrap:wrap;">
                            <button wire:click="abrirFormulario({{ $u->id }})" class="link-action">Editar</button>

                            <button wire:click="restablecerPassword({{ $u->id }})"
                                    wire:confirm="¿Restablecer contraseña al DNI del usuario ({{ $u->dni }})?"
                                    class="link-action"
                                    style="color:var(--color-warning);">
                                🔑 Reset
                            </button>

                            <button wire:click="toggleBloqueo({{ $u->id }})"
                                    wire:confirm="{{ (int)($u->bloqueado ?? 0) ? '¿Desbloquear a ' . $u->nombre_completo . '?' : '¿Bloquear a ' . $u->nombre_completo . '?' }}"
                                    class="link-action {{ (int)($u->bloqueado ?? 0) ? '' : 'danger' }}">
                                {{ (int)($u->bloqueado ?? 0) ? '🔓 Desbloquear' : '🔒 Bloquear' }}
                            </button>

                            <button wire:click="desactivar({{ $u->id }})"
                                    wire:confirm="¿Desactivar a {{ $u->nombre_completo }}? No podrá iniciar sesión."
                                    class="link-action danger">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
                        Sin usuarios
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($usuarios->hasPages())
        <div style="padding:.85rem 1.25rem; border-top:1px solid var(--color-border);">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($mostrarFormulario)
    @teleport('body')
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in" style="max-width:580px; max-height:92vh; overflow-y:auto;">
            <h2 class="modal-title">{{ $editandoId ? 'Editar' : 'Nuevo' }} Usuario</h2>

            <div style="display:flex; flex-direction:column; gap:.85rem;">

                {{-- Nombre y DNI --}}
                <div style="display:flex; gap:.75rem;">
                    <div style="flex:2;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Nombre completo</label>
                        <input wire:model="nombre_completo" class="input" placeholder="Nombre y apellidos">
                        @error('nombre_completo') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">DNI</label>
                        <input wire:model="dni" class="input" placeholder="DNI" maxlength="11">
                        @error('dni') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Password y Rol --}}
                <div style="display:flex; gap:.75rem;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">
                            Contraseña {{ $editandoId ? '(vacío = sin cambio)' : '' }}
                        </label>
                        <input wire:model="password" type="password" class="input" placeholder="Min. 6 caracteres">
                        @error('password') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Rol</label>
                        <select wire:model="id_rol" class="input">
                            <option value="">— Seleccionar —</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_rol') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Comercio --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Comercio</label>
                    @if($this->esAdmin())
                    <select wire:model="id_comercio" class="input">
                        <option value="0">— Seleccionar —</option>
                        @foreach($comercios as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                    @else
                    <div class="input" style="background:var(--color-surface-2); color:var(--color-text-muted); cursor:not-allowed;">
                        {{ $comercios->firstWhere('id', $id_comercio)?->nombre ?? '—' }}
                    </div>
                    @endif
                    @error('id_comercio') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>

                {{-- Bloqueo --}}
                <div style="background:var(--color-danger-light); border:1.5px solid {{ $bloqueado ? 'var(--color-danger)' : 'var(--color-border)' }};
                            border-radius:var(--radius-md); padding:.85rem 1rem; transition:all var(--transition-base);">
                    <label style="display:flex; align-items:center; gap:.6rem; cursor:pointer; margin-bottom:0;">
                        <input type="checkbox" wire:model.live="bloqueado" value="1"
                               style="width:16px; height:16px; accent-color:var(--color-danger);">
                        <span style="font-size:.875rem; font-weight:600; color:var(--color-danger);">
                            🔒 Bloquear acceso a este usuario
                        </span>
                    </label>
                    @if($bloqueado)
                    <div class="animate-fade-in" style="margin-top:.65rem;">
                        <label style="display:block; font-size:.78rem; font-weight:600; color:var(--color-danger); margin-bottom:.3rem;">
                            Motivo del bloqueo (se mostrará al usuario)
                        </label>
                        <textarea wire:model="motivo_bloqueo"
                                  class="input"
                                  rows="2"
                                  placeholder="Ej: Cuenta suspendida por revisión interna."
                                  style="resize:none; border-color:var(--color-danger);"></textarea>
                    </div>
                    @endif
                </div>

                {{-- Menús --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.5rem;">Acceso a menús</label>
                    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:.4rem;">
                        @foreach($menus as $m)
                        <label style="
                            display:flex; align-items:center; gap:.5rem;
                            padding:.4rem .6rem; border-radius:var(--radius-md); cursor:pointer;
                            border:1.5px solid {{ in_array((string)$m->id, $menusSeleccionados) ? 'var(--color-turquesa)' : 'var(--color-border)' }};
                            background:{{ in_array((string)$m->id, $menusSeleccionados) ? 'var(--color-turquesa-muted)' : 'transparent' }};
                            transition:all var(--transition-fast);">
                            <input type="checkbox"
                                   wire:model="menusSeleccionados"
                                   value="{{ $m->id }}"
                                   style="accent-color:var(--color-turquesa);">
                            <span style="font-size:.85rem; display:flex; align-items:center; gap:.35rem;">
                                {!! \App\Helpers\IconoHelper::get($m->icono ?? $m->ruta) !!}
                                {{ $m->nombre }}
                            </span>
                        </label>
                        @endforeach
                    </div>
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