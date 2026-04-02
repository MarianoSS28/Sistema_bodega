<div class="animate-fade-in">

    {{-- CABECERA --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.5rem;">
        <div>
            <h1 class="page-title" style="margin:0;">🏷️ Ofertas y Promociones</h1>
            <p style="margin:.15rem 0 0; font-size:.82rem; color:var(--color-text-secondary);">
                Configura descuentos y combos para tus productos
            </p>
        </div>
        <button wire:click="abrirFormulario()" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva oferta
        </button>
    </div>

    @if(session('ok'))
        <div class="alert alert-success animate-fade-in" style="margin-bottom:1.25rem;">✓ {{ session('ok') }}</div>
    @endif

    {{-- BUSCADOR --}}
    <div class="card" style="padding:.85rem 1rem; margin-bottom:1.25rem;">
        <div style="position:relative; max-width:360px;">
            <svg style="position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:var(--color-text-secondary); pointer-events:none;"
                 width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input wire:model.live.debounce.300ms="busqueda" type="text"
                   placeholder="Buscar oferta…" class="input" style="padding-left:2.2rem;">
        </div>
    </div>

    {{-- GRID DE OFERTAS --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1rem; margin-bottom:1.5rem;">
        @forelse($ofertas as $o)
        @php
            $hoy        = now()->toDateString();
            $vigente    = (!$o->fecha_inicio || $o->fecha_inicio <= $hoy)
                       && (!$o->fecha_fin    || $o->fecha_fin    >= $hoy);
            $colorTipo  = match($o->tipo) {
                '2x1'        => ['bg' => '#d4f5e5', 'fg' => '#1d9458', 'icon' => '2️⃣'],
                'nxm'        => ['bg' => '#e8f8ff', 'fg' => '#0ea5e9', 'icon' => '🎁'],
                'porcentaje' => ['bg' => '#fffbeb', 'fg' => '#d97706', 'icon' => '%'],
                'fijo'       => ['bg' => '#fdf1ef', 'fg' => '#e0513a', 'icon' => '💰'],
                default      => ['bg' => '#f5f5f5', 'fg' => '#888',    'icon' => '🏷️'],
            };
        @endphp
        <div class="card animate-fade-in"
             style="padding:1.25rem; position:relative; overflow:hidden;
                    opacity:{{ $o->activa ? '1' : '0.55' }};">

            {{-- Banda lateral de color --}}
            <div style="position:absolute; top:0; left:0; bottom:0; width:5px;
                        background:{{ $colorTipo['fg'] }};"></div>

            <div style="padding-left:.5rem;">
                {{-- Header --}}
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; margin-bottom:.75rem;">
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; margin-bottom:.25rem;">
                            <span style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                                          letter-spacing:.06em; padding:.2rem .6rem; border-radius:99px;
                                          background:{{ $colorTipo['bg'] }}; color:{{ $colorTipo['fg'] }};">
                                {{ $colorTipo['icon'] }} {{ $o->tipo_label }}
                            </span>
                            @if(!$o->activa)
                                <span class="badge" style="background:#f5f5f5; color:#888; font-size:.68rem;">Pausada</span>
                            @elseif(!$vigente)
                                <span class="badge badge-warning" style="font-size:.68rem;">Fuera de vigencia</span>
                            @else
                                <span class="badge badge-success" style="font-size:.68rem;">Activa</span>
                            @endif
                        </div>
                        <p style="font-weight:700; font-size:.95rem; color:var(--color-text-primary);">
                            {{ $o->nombre }}
                        </p>
                        @if($o->descripcion)
                        <p style="font-size:.78rem; color:var(--color-text-secondary); margin-top:.15rem;">
                            {{ $o->descripcion }}
                        </p>
                        @endif
                    </div>

                    {{-- Toggle activa --}}
                    <button wire:click="toggleActiva({{ $o->id }})"
                            title="{{ $o->activa ? 'Pausar oferta' : 'Activar oferta' }}"
                            style="
                                width:40px; height:22px; border-radius:99px; border:none; cursor:pointer;
                                background:{{ $o->activa ? 'var(--color-turquesa)' : 'var(--color-border)' }};
                                position:relative; transition:background var(--transition-base); flex-shrink:0;
                            ">
                        <span style="
                            position:absolute; top:2px;
                            left:{{ $o->activa ? '20px' : '2px' }};
                            width:18px; height:18px;
                            background:#fff; border-radius:50%;
                            transition:left var(--transition-base);
                            box-shadow:0 1px 3px rgba(0,0,0,.2);
                        "></span>
                    </button>
                </div>

                {{-- Fechas vigencia --}}
                @if($o->fecha_inicio || $o->fecha_fin)
                <div style="display:flex; gap:.5rem; align-items:center; margin-bottom:.65rem;
                             font-size:.75rem; color:var(--color-text-secondary);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    @if($o->fecha_inicio) {{ \Carbon\Carbon::parse($o->fecha_inicio)->format('d/m/Y') }} @else Sin inicio @endif
                    →
                    @if($o->fecha_fin) {{ \Carbon\Carbon::parse($o->fecha_fin)->format('d/m/Y') }} @else Sin fin @endif
                </div>
                @endif

                {{-- Productos incluidos --}}
                <div style="margin-bottom:.85rem;">
                    <p style="font-size:.72rem; font-weight:700; text-transform:uppercase;
                               letter-spacing:.05em; color:var(--color-text-muted); margin-bottom:.35rem;">
                        Productos ({{ $o->productos->count() }})
                    </p>
                    @if($o->productos->isEmpty())
                        <p style="font-size:.78rem; color:var(--color-text-muted);">Sin productos asignados</p>
                    @else
                    <div style="display:flex; flex-wrap:wrap; gap:.3rem;">
                        @foreach($o->productos->take(4) as $p)
                        <span style="font-size:.72rem; background:var(--color-surface-2); color:var(--color-text-secondary);
                                      padding:.15rem .5rem; border-radius:99px; border:1px solid var(--color-border);">
                            {{ $p->nombre }}
                        </span>
                        @endforeach
                        @if($o->productos->count() > 4)
                        <span style="font-size:.72rem; background:var(--color-turquesa-muted); color:var(--color-turquesa-dark);
                                      padding:.15rem .5rem; border-radius:99px;">
                            +{{ $o->productos->count() - 4 }} más
                        </span>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Acciones --}}
                <div style="display:flex; gap:.5rem; border-top:1px solid var(--color-border); padding-top:.65rem;">
                    <button wire:click="abrirFormulario({{ $o->id }})" class="link-action" style="font-size:.78rem;">
                        ✏️ Editar
                    </button>
                    <button wire:click="eliminar({{ $o->id }})"
                            wire:confirm="¿Eliminar la oferta '{{ $o->nombre }}'?"
                            class="link-action danger" style="font-size:.78rem; margin-left:auto;">
                        🗑️ Eliminar
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1; text-align:center; padding:3rem 1rem; color:var(--color-text-muted);">
            <div style="font-size:2.5rem; margin-bottom:.75rem;">🏷️</div>
            <p style="font-size:.9rem; font-weight:600;">Sin ofertas creadas</p>
            <p style="font-size:.8rem; margin-top:.25rem;">Crea tu primera oferta para aplicar descuentos automáticos en ventas</p>
        </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($ofertas->hasPages())
    <div style="padding:.85rem 1rem; background:var(--color-surface); border-radius:var(--radius-lg); border:1px solid var(--color-border);">
        {{ $ofertas->links() }}
    </div>
    @endif

    {{-- ══ MODAL FORMULARIO ══ --}}
    @if($mostrarFormulario)
    @teleport('body')
    <div class="modal-backdrop animate-fade-in" wire:click.self="$set('mostrarFormulario', false)">
        <div class="modal-box animate-scale-in" style="max-width:620px; width:100%; max-height:92vh; overflow-y:auto;">

            {{-- Header --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
                <h2 class="modal-title" style="margin:0;">
                    {{ $editandoId ? '✏️ Editar oferta' : '🏷️ Nueva oferta' }}
                </h2>
                <button wire:click="$set('mostrarFormulario', false)"
                        style="background:none; border:none; cursor:pointer; color:var(--color-text-secondary);
                               padding:.25rem; border-radius:var(--radius-sm);" aria-label="Cerrar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div style="display:flex; flex-direction:column; gap:1rem;">

                {{-- Nombre --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Nombre de la oferta <span style="color:var(--color-danger)">*</span>
                    </label>
                    <input wire:model="nombre" type="text" class="input" placeholder="Ej: 2x1 en gaseosas de verano" autofocus>
                    @error('nombre') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>

                {{-- Descripción --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Descripción</label>
                    <textarea wire:model="descripcion" class="input" rows="2" style="resize:none;"
                              placeholder="Detalle opcional de la promoción…"></textarea>
                </div>

                {{-- Tipo --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.5rem;">
                        Tipo de oferta <span style="color:var(--color-danger)">*</span>
                    </label>
                    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:.5rem;">
                        @foreach([
                            ['2x1',       '2️⃣',  '2×1',            'Lleva 2 paga 1'],
                            ['nxm',       '🎁',  'N×M personalizado', 'Compra N lleva M'],
                            ['porcentaje','%',   'Porcentaje',     'Ej: 20% de descuento'],
                            ['fijo',      '💰',  'Descuento fijo', 'Ej: S/ 1.00 menos'],
                        ] as [$val, $ico, $label, $desc])
                        <button wire:click="$set('tipo', '{{ $val }}')"
                                type="button"
                                style="
                                    display:flex; align-items:flex-start; gap:.6rem;
                                    padding:.65rem .75rem; border-radius:var(--radius-md);
                                    border:2px solid {{ $tipo === $val ? 'var(--color-turquesa)' : 'var(--color-border)' }};
                                    background:{{ $tipo === $val ? 'var(--color-turquesa-muted)' : 'var(--color-surface)' }};
                                    cursor:pointer; text-align:left; transition:all var(--transition-fast);
                                ">
                            <span style="font-size:1.1rem; flex-shrink:0; line-height:1.2;">{{ $ico }}</span>
                            <div>
                                <p style="font-size:.82rem; font-weight:700; color:{{ $tipo === $val ? 'var(--color-turquesa-dark)' : 'var(--color-text-primary)' }}; margin-bottom:.1rem;">{{ $label }}</p>
                                <p style="font-size:.72rem; color:var(--color-text-muted);">{{ $desc }}</p>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Valor según tipo --}}
                @if($tipo === 'porcentaje')
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Porcentaje de descuento (%) <span style="color:var(--color-danger)">*</span>
                    </label>
                    <div style="position:relative; max-width:180px;">
                        <input wire:model="valor" type="number" min="1" max="100" step="1"
                               class="input" placeholder="Ej: 20" style="padding-right:2.2rem;">
                        <span style="position:absolute; right:.85rem; top:50%; transform:translateY(-50%);
                                      font-weight:700; color:var(--color-text-secondary);">%</span>
                    </div>
                    @error('valor') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>

                @elseif($tipo === 'fijo')
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Descuento fijo (S/) <span style="color:var(--color-danger)">*</span>
                    </label>
                    <div style="position:relative; max-width:180px;">
                        <span style="position:absolute; left:.85rem; top:50%; transform:translateY(-50%);
                                      font-weight:700; color:var(--color-text-secondary);">S/</span>
                        <input wire:model="valor" type="number" min="0.01" step="0.01"
                               class="input" placeholder="0.00" style="padding-left:2.2rem;">
                    </div>
                    @error('valor') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>

                @elseif($tipo === 'nxm')
                <div style="display:flex; gap:.75rem; flex-wrap:wrap;">
                    <div style="flex:1; min-width:120px;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Paga (compra)</label>
                        <input wire:model="cantidad_paga" type="number" min="1" class="input" placeholder="Ej: 2">
                        @error('cantidad_paga') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                    <div style="flex:1; min-width:120px;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Lleva (recibe)</label>
                        <input wire:model="cantidad_lleva" type="number" min="1" class="input" placeholder="Ej: 3">
                        @error('cantidad_lleva') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div style="padding:.5rem .75rem; background:var(--color-turquesa-muted); border-radius:var(--radius-md);
                             font-size:.78rem; color:var(--color-turquesa-dark);">
                    💡 Compra <strong>{{ $cantidad_paga }}</strong> y lleva <strong>{{ $cantidad_lleva }}</strong>
                    ({{ max(0, (int)$cantidad_lleva - (int)$cantidad_paga) }} gratis)
                </div>

                @else {{-- 2x1 --}}
                <div style="padding:.75rem 1rem; background:var(--color-turquesa-muted); border-radius:var(--radius-md);
                             font-size:.82rem; color:var(--color-turquesa-dark); font-weight:500;">
                    ✅ Con el 2×1 el cliente paga 1 de cada 2 unidades del mismo producto.
                </div>
                @endif

                {{-- Fechas --}}
                <div style="display:flex; gap:.75rem; flex-wrap:wrap;">
                    <div style="flex:1; min-width:150px;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Fecha inicio</label>
                        <input wire:model="fecha_inicio" type="date" class="input">
                    </div>
                    <div style="flex:1; min-width:150px;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Fecha fin</label>
                        <input wire:model="fecha_fin" type="date" class="input">
                    </div>
                </div>
                <p style="font-size:.72rem; color:var(--color-text-muted); margin-top:-.5rem;">
                    Deja vacío para que la oferta no tenga límite de tiempo.
                </p>

                {{-- Productos --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.5rem;">
                        Productos incluidos en la oferta
                    </label>
                    <div style="max-height:200px; overflow-y:auto; border:1.5px solid var(--color-border);
                                border-radius:var(--radius-md); padding:.5rem;">
                        @foreach($todosProductos as $p)
                        <label style="
                            display:flex; align-items:center; gap:.6rem;
                            padding:.35rem .5rem; border-radius:var(--radius-sm); cursor:pointer;
                            background:{{ in_array((string)$p->id, $productosSeleccionados) ? 'var(--color-turquesa-muted)' : 'transparent' }};
                            border:1px solid {{ in_array((string)$p->id, $productosSeleccionados) ? 'var(--color-turquesa)' : 'transparent' }};
                            transition:all var(--transition-fast); margin-bottom:.2rem;
                        ">
                            <input type="checkbox"
                                   wire:model="productosSeleccionados"
                                   value="{{ $p->id }}"
                                   style="accent-color:var(--color-turquesa);">
                            <div style="flex:1; min-width:0;">
                                <span style="font-size:.85rem; font-weight:500; color:var(--color-text-primary);">
                                    {{ $p->nombre }}
                                </span>
                                <span style="font-size:.75rem; color:var(--color-text-muted); margin-left:.4rem;">
                                    S/ {{ number_format($p->precio, 2) }}
                                </span>
                            </div>
                        </label>
                        @endforeach
                        @if($todosProductos->isEmpty())
                            <p style="text-align:center; padding:1rem; color:var(--color-text-muted); font-size:.82rem;">
                                Sin productos disponibles
                            </p>
                        @endif
                    </div>
                    @if(count($productosSeleccionados) > 0)
                    <p style="font-size:.75rem; color:var(--color-turquesa-dark); margin-top:.35rem; font-weight:600;">
                        ✓ {{ count($productosSeleccionados) }} producto(s) seleccionado(s)
                    </p>
                    @endif
                </div>

            </div>

            {{-- Footer --}}
            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.5rem; border-top:1px solid var(--color-border); padding-top:1rem;">
                <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="guardar" wire:loading.attr="disabled" class="btn btn-primary">
                    <span wire:loading.remove wire:target="guardar">
                        {{ $editandoId ? 'Guardar cambios' : 'Crear oferta' }}
                    </span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </button>
            </div>

        </div>
    </div>
    @endteleport
    @endif

</div>