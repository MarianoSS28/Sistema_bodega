<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">Menus del sistema</h1>
        <button wire:click="abrirFormulario()" class="btn btn-primary">+ Nuevo Menu</button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; {{ session('ok') }}</div>
    @endif

    {{-- Referencia de claves de icono --}}
    <div class="card animate-fade-in delay-50"
         style="padding:.85rem 1.25rem; margin-bottom:1.25rem;
                border-left:4px solid var(--color-celeste-dark);">
        <p style="font-size:.78rem; font-weight:700; color:var(--color-celeste-dark); margin-bottom:.4rem;">
            Claves de icono disponibles
        </p>
        <p style="font-size:.78rem; color:var(--color-text-muted); line-height:1.7;">
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">dashboard</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">ventas</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">productos</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">historial</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">vouchers</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">usuarios</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">comercio</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">comercios-admin</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">menus</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">roles</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">terminos</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">tickets</code>
            <code style="background:var(--color-surface-2); padding:.1rem .35rem; border-radius:4px;">mantenimiento-admin</code>
        </p>
    </div>

    <div class="table-wrap animate-fade-in delay-100">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Icono</th>
                    <th>Nombre</th>
                    <th>Ruta (nombre Laravel)</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $m)
                <tr>
                    <td style="font-family:monospace; color:var(--color-text-muted);">{{ $m->id }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:.5rem;">
                            <span style="color:var(--color-text-secondary);">
                                {!! \App\Helpers\IconoHelper::get($m->icono ?? $m->ruta) !!}
                            </span>
                            <code style="font-size:.75rem; color:var(--color-text-muted);">{{ $m->icono }}</code>
                        </div>
                    </td>
                    <td style="font-weight:600;">{{ $m->nombre }}</td>
                    <td>
                        <code style="font-size:.82rem; color:var(--color-celeste-dark);
                                     background:var(--color-celeste-muted); padding:.15rem .45rem;
                                     border-radius:4px;">{{ $m->ruta }}</code>
                    </td>
                    <td class="text-center">
                        <div style="display:flex; gap:.5rem; justify-content:center;">
                            <button wire:click="abrirFormulario({{ $m->id }})" class="link-action">Editar</button>
                            <button wire:click="desactivar({{ $m->id }})"
                                    wire:confirm="Eliminar el menu {{ $m->nombre }}?"
                                    class="link-action danger">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
                        Sin menus registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($mostrarFormulario)
    @teleport('body')
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in" style="max-width:440px;">
            <h2 class="modal-title">{{ $editandoId ? 'Editar' : 'Nuevo' }} Menu</h2>

            <div style="display:flex; flex-direction:column; gap:.85rem;">
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Nombre</label>
                    <input wire:model="nombre" class="input" placeholder="Ej: Productos">
                    @error('nombre') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Ruta (nombre de la ruta Laravel)</label>
                    <input wire:model="ruta" class="input" placeholder="Ej: productos">
                    @error('ruta') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Clave de icono</label>
                    <input wire:model="icono" class="input" placeholder="Ej: productos">
                    <p style="font-size:.75rem; color:var(--color-text-muted); margin-top:.3rem;">
                        Usa las claves de la lista de arriba. Si no coincide, no muestra icono.
                    </p>
                    @error('icono') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror

                    {{-- Preview del icono --}}
                    @if($icono)
                    <div style="margin-top:.5rem; display:flex; align-items:center; gap:.5rem;
                                 padding:.5rem .75rem; background:var(--color-surface-2);
                                 border-radius:var(--radius-md); border:1px solid var(--color-border);">
                        <span style="color:var(--color-turquesa);">
                            {!! \App\Helpers\IconoHelper::get($icono) !!}
                        </span>
                        <span style="font-size:.82rem; color:var(--color-text-secondary);">
                            {{ $icono ?: 'sin icono' }}
                        </span>
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