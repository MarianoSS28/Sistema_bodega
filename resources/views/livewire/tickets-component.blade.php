<div class="animate-fade-in" wire:poll.10s>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h1 class="page-title" style="margin-bottom:0;">
            Soporte / Tickets
        </h1>
        @if(!$this->esAdmin())
        <button wire:click="abrirFormulario" class="btn btn-primary">+ Nuevo Ticket</button>
        @endif
    </div>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; {{ session('ok') }}</div>
    @endif

    {{-- Filtro estado (solo admin) --}}
    @if($this->esAdmin())
    <div style="display:flex; gap:.75rem; align-items:center; margin-bottom:1rem;">
        <label style="font-size:.82rem; font-weight:600; color:var(--color-text-muted);">Filtrar por estado:</label>
        <select wire:model.live="filtroEstado" class="input" style="width:auto;">
            <option value="">Todos</option>
            <option value="1">Abierto</option>
            <option value="2">En proceso</option>
            <option value="3">Cerrado</option>
        </select>
        <button wire:click="abrirFormulario" class="btn btn-primary" style="margin-left:auto;">+ Nuevo Ticket</button>
    </div>
    @endif

    <div style="display:grid; grid-template-columns:{{ $ticketSeleccionado ? '1fr 1.4fr' : '1fr' }}; gap:1.25rem; align-items:start;">

        {{-- Lista de tickets --}}
        <div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>N°</th>
                            @if($this->esAdmin())
                            <th>Comercio</th>
                            @endif
                            <th>Asunto</th>
                            <th class="text-center">Prior.</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Resp.</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $t)
                        @php
                            $numero = 'T-' . $t->id_comercio . str_pad($t->id, 8, '0', STR_PAD_LEFT);
                            $activo = $ticketSeleccionado && $ticketSeleccionado->id === $t->id;
                        @endphp
                        <tr style="{{ $activo ? 'background:var(--color-turquesa-muted);' : '' }}">
                            <td style="font-family:monospace; font-size:.78rem; color:var(--color-celeste-dark);">
                                {{ $numero }}
                            </td>
                            @if($this->esAdmin())
                            <td style="font-size:.82rem;">{{ $t->comercio->nombre ?? '—' }}</td>
                            @endif
                            <td style="font-weight:500; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $t->asunto }}
                            </td>
                            <td class="text-center">
                                @if($t->prioridad === 'urgente')
                                    <span class="badge badge-danger">Urgente</span>
                                @elseif($t->prioridad === 'alta')
                                    <span class="badge badge-warning">Alta</span>
                                @else
                                    <span class="badge badge-info">Normal</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if((int)$t->estado === 1)
                                    <span class="badge badge-success">Abierto</span>
                                @elseif((int)$t->estado === 2)
                                    <span class="badge badge-warning">En proceso</span>
                                @else
                                    <span class="badge" style="background:var(--color-border); color:var(--color-text-secondary);">Cerrado</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $t->total_respuestas ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <button wire:click="abrirTicket({{ $t->id }})" class="link-action">
                                    {{ $activo ? 'Cerrar' : 'Ver' }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $this->esAdmin() ? 7 : 6 }}"
                                style="text-align:center; padding:2.5rem; color:var(--color-text-muted);">
                                Sin tickets registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($tickets->hasPages())
                <div style="padding:.85rem 1.25rem; border-top:1px solid var(--color-border);">
                    {{ $tickets->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Hilo del ticket seleccionado --}}
        @if($ticketSeleccionado)
        <div class="card animate-fade-in" style="padding:0; overflow:hidden;">
            {{-- Header --}}
            <div style="padding:1rem 1.25rem; background:var(--gradient-soft); border-bottom:1px solid var(--color-border);">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <p style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-text-muted); margin-bottom:.2rem;">
                            T-{{ $ticketSeleccionado->id_comercio }}{{ str_pad($ticketSeleccionado->id, 8, '0', STR_PAD_LEFT) }}
                        </p>
                        <p style="font-weight:700; font-size:.95rem; color:var(--color-text-primary);">
                            {{ $ticketSeleccionado->asunto }}
                        </p>
                        <p style="font-size:.78rem; color:var(--color-text-muted); margin-top:.2rem;">
                            Por {{ $ticketSeleccionado->usuario->nombre_completo ?? '—' }}
                            · {{ \Carbon\Carbon::parse($ticketSeleccionado->fecha_creacion)->format('d/m/Y H:i') }}
                            @if($this->esAdmin())
                            · {{ $ticketSeleccionado->comercio->nombre ?? '' }}
                            @endif
                        </p>
                    </div>
                    @if($this->esAdmin() && (int)$ticketSeleccionado->estado !== 3)
                    <button wire:click="cerrarTicket({{ $ticketSeleccionado->id }})"
                            wire:confirm="Cerrar este ticket?"
                            class="btn btn-secondary" style="font-size:.78rem; padding:.3rem .8rem;">
                        Cerrar ticket
                    </button>
                    @endif
                </div>
            </div>

            {{-- Mensaje original --}}
            <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--color-border);
                        background:var(--color-turquesa-muted);">
                <p style="font-size:.78rem; font-weight:600; color:var(--color-text-muted); margin-bottom:.4rem;">
                    Mensaje original
                </p>
                <p style="font-size:.875rem; line-height:1.6; white-space:pre-wrap; color:var(--color-text-primary);">{{ $ticketSeleccionado->mensaje }}</p>
            </div>

            {{-- Hilo de respuestas --}}
            <div style="max-height:320px; overflow-y:auto; padding:.75rem 1.25rem; display:flex; flex-direction:column; gap:.75rem;">
                @if(session('ok_respuesta'))
                <div class="alert alert-success">&#10003; {{ session('ok_respuesta') }}</div>
                @endif

                @forelse($mensajes as $m)
                @php $esMio = $m->id_usuario === Auth::id(); @endphp
                <div style="display:flex; flex-direction:column; align-items:{{ $esMio ? 'flex-end' : 'flex-start' }};">
                    <div style="
                        max-width:85%;
                        background:{{ $esMio ? 'var(--color-turquesa)' : 'var(--color-surface-2)' }};
                        color:{{ $esMio ? '#fff' : 'var(--color-text-primary)' }};
                        border:1px solid {{ $esMio ? 'transparent' : 'var(--color-border)' }};
                        border-radius:{{ $esMio ? 'var(--radius-lg) var(--radius-lg) var(--radius-sm) var(--radius-lg)' : 'var(--radius-lg) var(--radius-lg) var(--radius-lg) var(--radius-sm)' }};
                        padding:.65rem .9rem;
                        font-size:.875rem; line-height:1.5; white-space:pre-wrap;
                    ">{{ $m->mensaje }}</div>
                    <p style="font-size:.7rem; color:var(--color-text-muted); margin-top:.2rem;">
                        {{ $m->nombre_completo }}
                        · {{ \Carbon\Carbon::parse($m->fecha_creacion)->format('d/m/Y H:i') }}
                    </p>
                </div>
                @empty
                <p style="text-align:center; color:var(--color-text-muted); font-size:.85rem; padding:1rem 0;">
                    Sin respuestas aun
                </p>
                @endforelse
            </div>

            {{-- Caja de respuesta --}}
            @if((int)$ticketSeleccionado->estado !== 3)
            <div style="padding:1rem 1.25rem; border-top:1px solid var(--color-border); background:var(--color-surface);">
                <textarea wire:model="respuesta"
                          class="input"
                          rows="3"
                          placeholder="Escribe tu respuesta..."
                          style="resize:none; margin-bottom:.65rem;"></textarea>
                @error('respuesta')
                    <span style="color:var(--color-danger); font-size:.78rem; display:block; margin-bottom:.4rem;">{{ $message }}</span>
                @enderror
                <div style="display:flex; justify-content:flex-end;">
                    <button wire:click="responder" class="btn btn-primary">Enviar respuesta</button>
                </div>
            </div>
            @else
            <div style="padding:.75rem 1.25rem; text-align:center; background:var(--color-surface-2);
                        border-top:1px solid var(--color-border); color:var(--color-text-muted); font-size:.85rem;">
                Este ticket esta cerrado
            </div>
            @endif
        </div>
        @endif

    </div>

    {{-- Modal nuevo ticket --}}
    @if($mostrarFormulario)
    @teleport('body')
    <div class="modal-backdrop">
        <div class="modal-box animate-scale-in" style="max-width:520px;">
            <h2 class="modal-title">Nuevo Ticket de Soporte</h2>

            <div style="display:flex; flex-direction:column; gap:.85rem;">
                <div style="display:flex; gap:.75rem; align-items:flex-end;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Asunto</label>
                        <input wire:model="asunto" class="input" placeholder="Describe brevemente el problema">
                        @error('asunto') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                    <div style="width:120px;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Prioridad</label>
                        <select wire:model="prioridad" class="input">
                            <option value="normal">Normal</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                        @error('prioridad') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Descripcion detallada</label>
                    <textarea wire:model="mensaje" class="input" rows="5"
                              placeholder="Explica el problema con el mayor detalle posible..."
                              style="resize:vertical;"></textarea>
                    @error('mensaje') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="crearTicket" class="btn btn-primary">Enviar Ticket</button>
            </div>
        </div>
    </div>
    @endteleport
    @endif
</div>