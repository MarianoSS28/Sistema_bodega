<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">📢 Comunicados</h1>
        <button wire:click="abrirFormulario()" class="btn btn-primary">+ Nuevo Comunicado</button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">✓ {{ session('ok') }}</div>
    @endif

    <div class="table-wrap animate-fade-in delay-100">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Creado por</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comunicados as $c)
                <tr>
                    <td style="font-family:monospace; color:var(--color-text-muted);">{{ $c->id }}</td>
                    <td style="font-weight:600;">{{ $c->titulo }}</td>
                    <td style="font-size:.85rem; color:var(--color-text-secondary);">
                        {{ \Carbon\Carbon::parse($c->fecha_creacion)->format('d/m/Y H:i') }}
                    </td>
                    <td style="font-size:.82rem; color:var(--color-text-muted);">{{ $c->usuario_creacion }}</td>
                    <td class="text-center">
                        <div style="display:flex; gap:.5rem; justify-content:center;">
                            <button wire:click="abrirFormulario({{ $c->id }})" class="link-action">Editar</button>
                            <button wire:click="desactivar({{ $c->id }})"
                                    wire:confirm="¿Eliminar este comunicado?"
                                    class="link-action danger">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">Sin comunicados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mostrarFormulario)
    @teleport('body')
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in" style="max-width:680px; width:100%;">
            <h2 class="modal-title">{{ $editandoId ? 'Editar' : 'Nuevo' }} Comunicado</h2>
            <div style="display:flex; flex-direction:column; gap:.85rem;">
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Título</label>
                    <input wire:model="titulo" class="input" placeholder="Título del comunicado">
                    @error('titulo') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Contenido</label>
                    <textarea wire:model="contenido" class="input" rows="8"
                              placeholder="Escribe el mensaje del comunicado..."
                              style="resize:vertical;"></textarea>
                    @error('contenido') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="guardar" class="btn btn-primary">Guardar y publicar</button>
            </div>
        </div>
    </div>
    @endteleport
    @endif
</div>