<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">Roles</h1>
        <button wire:click="abrirFormulario()" class="btn btn-primary">+ Nuevo Rol</button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; {{ session('ok') }}</div>
    @endif

    <div class="table-wrap animate-fade-in delay-100" style="max-width:540px;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $r)
                <tr>
                    <td style="font-family:monospace; color:var(--color-text-muted);">{{ $r->id }}</td>
                    <td style="font-weight:500;">{{ $r->nombre }}</td>
                    <td class="text-center">
                        <div style="display:flex; gap:.5rem; justify-content:center;">
                            <button wire:click="abrirFormulario({{ $r->id }})" class="link-action">Editar</button>
                            <button wire:click="desactivar({{ $r->id }})"
                                    wire:confirm="¿Eliminar el rol {{ $r->nombre }}?"
                                    class="link-action danger">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
                        Sin roles registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($mostrarFormulario)
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in" style="max-width:380px;">
            <h2 class="modal-title">{{ $editandoId ? 'Editar' : 'Nuevo' }} Rol</h2>

            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Nombre del rol</label>
                <input wire:model="nombre" wire:keydown.enter="guardar" class="input" placeholder="Ej: Administrador">
                @error('nombre') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="guardar" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
    @endif
</div>