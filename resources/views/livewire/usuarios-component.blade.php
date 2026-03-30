<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">👥 Usuarios</h1>
        <button wire:click="abrirFormulario()" class="btn btn-primary">+ Nuevo Usuario</button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">✅ {{ session('ok') }}</div>
    @endif

    <input wire:model.live="busqueda" placeholder="🔍 Buscar por nombre o DNI..."
           class="input" style="margin-bottom:1rem; max-width:380px;">

    <div class="table-wrap animate-fade-in delay-100">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Rol</th>
                    <th>Menús</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                <tr>
                    <td style="font-weight:500;">{{ $u->nombre_completo }}</td>
                    <td style="font-family:monospace; color:var(--color-celeste-dark);">{{ $u->dni }}</td>
                    <td><span class="badge badge-info">{{ $u->rol->nombre ?? '—' }}</span></td>
                    <td style="font-size:.8rem; color:var(--color-text-muted);">
                        {{ $u->menus()->count() }} menú(s)
                    </td>
                    <td class="text-center">
                        <div style="display:flex; gap:.5rem; justify-content:center;">
                            <button wire:click="abrirFormulario({{ $u->id }})" class="link-action">Editar</button>
                            <button wire:click="desactivar({{ $u->id }})"
                                    wire:confirm="¿Desactivar este usuario?"
                                    class="link-action danger">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">Sin usuarios</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($usuarios->hasPages())
        <div style="padding:.85rem 1.25rem; border-top:1px solid var(--color-border);">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>

    @if($mostrarFormulario)
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in" style="max-width:520px;">
            <h2 class="modal-title">{{ $editandoId ? '✏️ Editar' : '➕ Nuevo' }} Usuario</h2>

            <div style="display:flex; flex-direction:column; gap:.85rem;">
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

                <div style="display:flex; gap:.75rem;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Contraseña {{ $editandoId ? '(dejar vacío = sin cambio)' : '' }}</label>
                        <input wire:model="password" type="password" class="input" placeholder="Mínimo 6 caracteres">
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

                {{-- Menús --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.5rem;">Acceso a menús</label>
                    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:.4rem;">
                        @foreach($menus as $m)
                        <label style="display:flex; align-items:center; gap:.5rem; padding:.4rem .6rem; border-radius:var(--radius-md); cursor:pointer; border:1.5px solid {{ in_array((string)$m->id, $menusSeleccionados) ? 'var(--color-turquesa)' : 'var(--color-border)' }}; background:{{ in_array((string)$m->id, $menusSeleccionados) ? 'var(--color-turquesa-muted)' : 'transparent' }}; transition:all var(--transition-fast);">
                            <input type="checkbox" wire:model="menusSeleccionados" value="{{ $m->id }}"
                                   style="accent-color:var(--color-turquesa);">
                            <span style="font-size:.85rem;">{{ $m->icono }} {{ $m->nombre }}</span>
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
    @endif
</div>