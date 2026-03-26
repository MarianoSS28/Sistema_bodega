<div class="animate-fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">Productos</h1>
        <button wire:click="abrirFormulario()" class="btn btn-primary">
            + Nuevo Producto
        </button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">✅ {{ session('ok') }}</div>
    @endif

    <input wire:model.live="busqueda"
           placeholder="🔍 Buscar por nombre o código..."
           class="input"
           style="margin-bottom:1rem; max-width:380px;">

    <div class="table-wrap animate-fade-in delay-100">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Stock</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $p)
                <tr>
                    <td>
                        @if($p->foto_path)
                            <img src="{{ Storage::url($p->foto_path) }}"
                                 style="width:40px; height:40px; object-fit:cover; border-radius:var(--radius-md);">
                        @else
                            <div style="width:40px; height:40px; background:var(--color-turquesa-muted); border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; color:var(--color-text-muted); font-size:.7rem;">foto</div>
                        @endif
                    </td>
                    <td style="font-family:monospace; font-size:.82rem; color:var(--color-text-secondary);">{{ $p->codigo_barras }}</td>
                    <td style="font-weight:500;">{{ $p->nombre }}</td>
                    <td class="text-right" style="font-weight:600;">S/ {{ number_format($p->precio, 2) }}</td>
                    <td class="text-right">
                        @if($p->stock == 0)
                            <span class="badge badge-danger">0</span>
                        @elseif($p->stock <= 5)
                            <span class="badge badge-warning">{{ $p->stock }}</span>
                        @else
                            <span class="badge badge-success">{{ $p->stock }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div style="display:flex; gap:.5rem; justify-content:center;">
                            <button wire:click="abrirFormulario({{ $p->id }})" class="link-action">Editar</button>
                            <button wire:click="desactivar({{ $p->id }})"
                                    wire:confirm="¿Desactivar este producto?"
                                    class="link-action danger">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">Sin resultados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($mostrarFormulario)
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in">
            <h2 class="modal-title">{{ $editandoId ? '✏️ Editar' : '➕ Nuevo' }} Producto</h2>

            <div style="display:flex; flex-direction:column; gap:.85rem;">
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Nombre</label>
                    <input wire:model="nombre" class="input" placeholder="Nombre del producto">
                    @error('nombre') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Código de barras</label>
                    <input wire:model="codigo_barras" class="input" placeholder="Código">
                    @error('codigo_barras') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
                <div style="display:flex; gap:.75rem;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Precio</label>
                        <input wire:model="precio" type="number" step="0.01" class="input" placeholder="0.00">
                        @error('precio') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Stock</label>
                        <input wire:model="stock" type="number" class="input" placeholder="0">
                        @error('stock') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Foto</label>
                    @if($fotoActual)
                        <img src="{{ Storage::url($fotoActual) }}" style="width:64px; height:64px; object-fit:cover; border-radius:var(--radius-md); margin-bottom:.5rem; border:2px solid var(--color-border);">
                    @endif
                    @if($foto)
                        <img src="{{ $foto->temporaryUrl() }}" style="width:64px; height:64px; object-fit:cover; border-radius:var(--radius-md); margin-bottom:.5rem; border:2px solid var(--color-turquesa);">
                    @endif
                    <input type="file" wire:model="foto" accept="image/*" class="input">
                    @error('foto') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
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