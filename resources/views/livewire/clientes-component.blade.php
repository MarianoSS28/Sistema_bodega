<div>
    {{-- ── Flash ── --}}
    @if(session('ok'))
        <div class="alert alert-success animate-fade-in" style="margin-bottom:1.25rem;">
            <span>✓</span> {{ session('ok') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════
         CABECERA
    ══════════════════════════════════════════ --}}
    <div class="page-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.5rem;">
        <div>
            <h1 class="page-title" style="margin:0; font-size:1.35rem; font-weight:700;">
                Clientes
            </h1>
            <p style="margin:.15rem 0 0; font-size:.82rem; color:var(--color-text-secondary);">
                Personas registradas para fiados
            </p>
        </div>
        <button wire:click="abrirFormulario()" class="btn btn-primary" style="display:flex; align-items:center; gap:.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nuevo cliente
        </button>
    </div>

    {{-- ══════════════════════════════════════════
         BUSCADOR
    ══════════════════════════════════════════ --}}
    <div class="card" style="padding:.85rem 1rem; margin-bottom:1.25rem;">
        <div style="position:relative; max-width:360px;">
            <svg style="position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:var(--color-text-secondary); pointer-events:none;"
                 width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input wire:model.live.debounce.300ms="busqueda"
                   type="text" placeholder="Buscar por nombre…"
                   class="input" style="padding-left:2.2rem;">
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TABLA
    ══════════════════════════════════════════ --}}
    <div class="card" style="overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Notas</th>
                        <th>Fiados activos</th>
                        <th>Deuda total</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $c)
                        <tr wire:key="cliente-{{ $c->id }}">
                            <td style="color:var(--color-text-secondary); font-size:.8rem;">{{ $c->id }}</td>
                            <td style="font-weight:600;">{{ $c->nombre }}</td>
                            <td>{{ $c->telefono ?? '—' }}</td>
                            <td style="font-size:.82rem; color:var(--color-text-secondary); max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $c->notas ?? '—' }}
                            </td>
                            <td>
                                @php
                                    $activos = $c->fiados()->where('estado', 1)->count();
                                @endphp
                                @if($activos > 0)
                                    <span class="badge badge-warning">{{ $activos }}</span>
                                @else
                                    <span style="color:var(--color-text-secondary); font-size:.82rem;">0</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $deuda = $c->fiados()->where('estado', 1)->sum(\Illuminate\Support\Facades\DB::raw('total - total_pagado'));
                                @endphp
                                @if($deuda > 0)
                                    <span style="color:var(--color-danger); font-weight:700; font-size:.9rem;">
                                        S/ {{ number_format($deuda, 2) }}
                                    </span>
                                @else
                                    <span style="color:var(--color-success); font-size:.82rem;">Sin deuda</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; gap:.4rem; justify-content:flex-end;">
                                    <button wire:click="abrirFormulario({{ $c->id }})"
                                            class="btn btn-secondary btn-sm"
                                            title="Editar">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    @if($activos == 0)
                                    <button wire:click="desactivar({{ $c->id }})"
                                            wire:confirm="¿Eliminar cliente {{ $c->nombre }}?"
                                            class="btn btn-danger btn-sm"
                                            title="Eliminar">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:2.5rem; color:var(--color-text-secondary); font-size:.88rem;">
                                No hay clientes registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($clientes->hasPages())
            <div style="padding:.85rem 1rem; border-top:1px solid var(--color-border);">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         MODAL FORMULARIO
    ══════════════════════════════════════════ --}}
    @if($mostrarFormulario)
    <div class="modal-backdrop animate-fade-in" wire:click.self="$set('mostrarFormulario', false)">
        <div class="modal animate-slide-up" style="max-width:480px; width:100%;">

            <div class="modal-header">
                <h2 class="modal-title">
                    {{ $editandoId ? 'Editar cliente' : 'Nuevo cliente' }}
                </h2>
                <button wire:click="$set('mostrarFormulario', false)" class="modal-close" aria-label="Cerrar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="modal-body" style="display:flex; flex-direction:column; gap:1rem;">

                {{-- Nombre --}}
                <div class="form-group">
                    <label class="form-label">Nombre <span style="color:var(--color-danger)">*</span></label>
                    <input wire:model="nombre" type="text" class="input @error('nombre') input-error @enderror"
                           placeholder="Nombre del cliente" autofocus>
                    @error('nombre')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input wire:model="telefono" type="text" class="input @error('telefono') input-error @enderror"
                           placeholder="Ej: 987654321">
                    @error('telefono')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Notas --}}
                <div class="form-group">
                    <label class="form-label">Notas</label>
                    <textarea wire:model="notas" class="input @error('notas') input-error @enderror"
                              rows="2" placeholder="Información adicional…" style="resize:vertical;"></textarea>
                    @error('notas')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="modal-footer">
                <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">
                    Cancelar
                </button>
                <button wire:click="guardar" wire:loading.attr="disabled" class="btn btn-primary">
                    <span wire:loading.remove wire:target="guardar">
                        {{ $editandoId ? 'Guardar cambios' : 'Crear cliente' }}
                    </span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>