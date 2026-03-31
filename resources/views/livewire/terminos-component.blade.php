<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">Terminos y Condiciones</h1>
        <button wire:click="abrirFormulario()" class="btn btn-primary">+ Nueva Version</button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; {{ session('ok') }}</div>
    @endif

    {{-- Versión vigente --}}
    @if($terminoActivo)
    <div class="card animate-fade-in delay-50"
         style="padding:1rem 1.25rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:1rem;
                border-left:4px solid var(--color-turquesa);">
        <div style="flex:1;">
            <p style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-text-muted);">Version vigente</p>
            <p style="font-weight:700; font-size:.95rem; color:var(--color-text-primary);">
                v{{ $terminoActivo->version }} — {{ $terminoActivo->titulo }}
            </p>
        </div>
        <span class="badge badge-success">Vigente</span>
        <button wire:click="verDetalle({{ $terminoActivo->id }})" class="link-action">Ver contenido</button>
    </div>
    @endif

    {{-- Tabla historial --}}
    <div class="table-wrap animate-fade-in delay-100">
        <table>
            <thead>
                <tr>
                    <th>Version</th>
                    <th>Titulo</th>
                    <th>Fecha creacion</th>
                    <th>Creado por</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lista as $t)
                <tr>
                    <td>
                        <span class="badge {{ $loop->first ? 'badge-success' : 'badge-info' }}">
                            v{{ $t->version }}
                        </span>
                    </td>
                    <td style="font-weight:500;">{{ $t->titulo }}</td>
                    <td style="font-size:.85rem; color:var(--color-text-secondary);">
                        {{ \Carbon\Carbon::parse($t->fecha_creacion)->format('d/m/Y H:i') }}
                    </td>
                    <td style="font-size:.82rem; color:var(--color-text-muted);">{{ $t->usuario_creacion }}</td>
                    <td class="text-center">
                        <div style="display:flex; gap:.5rem; justify-content:center;">
                            <button wire:click="verDetalle({{ $t->id }})" class="link-action">Ver</button>
                            <button wire:click="abrirFormulario({{ $t->id }})" class="link-action">Editar</button>
                            <button wire:click="desactivar({{ $t->id }})"
                                    wire:confirm="Eliminar esta version?"
                                    class="link-action danger">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
                        Sin registros
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal edicion --}}
    @if($mostrarFormulario)
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in" style="max-width:680px; width:100%;">
            <h2 class="modal-title">{{ $editandoId ? 'Editar' : 'Nueva' }} Version de Terminos</h2>

            <div style="display:flex; flex-direction:column; gap:.85rem;">
                <div style="display:flex; gap:.75rem;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Titulo</label>
                        <input wire:model="titulo" class="input" placeholder="Titulo del documento">
                        @error('titulo') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                    <div style="width:110px;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Version</label>
                        <input wire:model="version" class="input" placeholder="1.0">
                        @error('version') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Contenido</label>
                    <textarea wire:model="contenido" class="input" rows="10"
                              placeholder="Escribe aqui el texto completo de los terminos y condiciones..."
                              style="resize:vertical; font-size:.85rem; line-height:1.6;"></textarea>
                    @error('contenido') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="guardar" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal vista previa --}}
    @if($mostrarVista && $vistaDetalle)
    <div class="modal-backdrop" style="z-index:200;" wire:click.self="cerrarVista">
        <div class="modal-box animate-scale-in" style="max-width:700px; width:100%; max-height:80vh; overflow-y:auto;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                <div>
                    <h2 style="font-size:1.1rem; font-weight:700; color:var(--color-text-primary);">
                        {{ $vistaDetalle->titulo }}
                    </h2>
                    <span class="badge badge-info" style="margin-top:.25rem;">v{{ $vistaDetalle->version }}</span>
                </div>
                <button wire:click="cerrarVista" class="btn btn-secondary" style="padding:.35rem .75rem;">X</button>
            </div>
            <div style="font-size:.875rem; line-height:1.8; color:var(--color-text-secondary);
                        white-space:pre-wrap; border-top:1px solid var(--color-border); padding-top:1rem;">
                {{ $vistaDetalle->contenido }}
            </div>
            <p style="font-size:.75rem; color:var(--color-text-muted); margin-top:1rem; text-align:right;">
                Creado por {{ $vistaDetalle->usuario_creacion }}
                el {{ \Carbon\Carbon::parse($vistaDetalle->fecha_creacion)->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
    @endif
</div>