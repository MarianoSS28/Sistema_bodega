<div>
    @if(session('ok'))
        <div class="alert alert-success animate-fade-in" style="margin-bottom:1.25rem;">
            ✓ {{ session('ok') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════
         CABECERA
    ══════════════════════════════════════════ --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.5rem;">
        <div>
            <h1 class="page-title" style="margin:0;">Clientes</h1>
            <p style="margin:.15rem 0 0; font-size:.82rem; color:var(--color-text-secondary);">
                Personas registradas para fiados
            </p>
        </div>
        <button wire:click="abrirFormulario()" class="btn btn-primary">
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
    <div class="table-wrap animate-fade-in delay-100">
        <table>
            <thead>
                <tr>
                    <th style="width:60px;">N°</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Notas</th>
                    <th class="text-center">Fiados activos</th>
                    <th class="text-right">Deuda total</th>
                    <th class="text-center" style="width:100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $c)
                @php
                    $activos = $c->fiados()->where('estado', 1)->count();
                    $deuda   = $c->fiados()->where('estado', 1)->sum(\Illuminate\Support\Facades\DB::raw('total - total_pagado'));
                @endphp
                <tr wire:key="cliente-{{ $c->id }}">

                    {{-- N° por comercio --}}
                    <td>
                        <span style="font-family:monospace; font-size:.82rem; font-weight:700;
                                     color:var(--color-turquesa);">
                            #{{ $c->nro_cliente ?? $c->id }}
                        </span>
                    </td>

                    {{-- Nombre --}}
                    <td style="font-weight:600;">{{ $c->nombre }}</td>

                    {{-- Teléfono --}}
                    <td style="font-size:.85rem; color:var(--color-text-secondary);">
                        {{ $c->telefono ?? '—' }}
                    </td>

                    {{-- Notas --}}
                    <td style="font-size:.82rem; color:var(--color-text-secondary);
                                max-width:200px; white-space:nowrap;
                                overflow:hidden; text-overflow:ellipsis;">
                        {{ $c->notas ?? '—' }}
                    </td>

                    {{-- Fiados activos --}}
                    <td class="text-center">
                        @if($activos > 0)
                            <span class="badge badge-warning">{{ $activos }}</span>
                        @else
                            <span style="color:var(--color-text-secondary); font-size:.82rem;">0</span>
                        @endif
                    </td>

                    {{-- Deuda --}}
                    <td class="text-right">
                        @if($deuda > 0)
                            <span style="color:var(--color-danger); font-weight:700; font-size:.9rem;">
                                S/ {{ number_format($deuda, 2) }}
                            </span>
                        @else
                            <span style="color:var(--color-turquesa); font-size:.82rem; font-weight:600;">
                                Sin deuda
                            </span>
                        @endif
                    </td>

                    {{-- Acciones --}}
                    <td class="text-center">
                        <div style="display:flex; gap:.4rem; justify-content:center;">
                            <button wire:click="abrirFormulario({{ $c->id }})"
                                    class="btn btn-secondary"
                                    style="padding:.3rem .65rem; font-size:.78rem;"
                                    title="Editar">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            @if($activos == 0)
                            <button wire:click="desactivar({{ $c->id }})"
                                    wire:confirm="¿Eliminar cliente {{ $c->nombre }}?"
                                    class="btn btn-danger"
                                    style="padding:.3rem .65rem; font-size:.78rem;"
                                    title="Eliminar">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M9 6V4h6v2"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:2.5rem;
                                           color:var(--color-text-secondary); font-size:.88rem;">
                        <div style="font-size:2rem; margin-bottom:.5rem;">👤</div>
                        No hay clientes registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

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
    @teleport('body')
    <div class="modal-backdrop animate-fade-in" wire:click.self="$set('mostrarFormulario', false)">
        <div class="modal-box animate-scale-in" style="max-width:480px; width:100%;">

            {{-- Header --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
                <h2 class="modal-title" style="margin:0;">
                    {{ $editandoId ? 'Editar cliente' : 'Nuevo cliente' }}
                </h2>
                <button wire:click="$set('mostrarFormulario', false)"
                        style="background:none; border:none; cursor:pointer;
                               color:var(--color-text-secondary); padding:.25rem;
                               border-radius:var(--radius-sm); line-height:1;"
                        aria-label="Cerrar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Campos --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">

                {{-- Nombre --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600;
                                   color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Nombre <span style="color:var(--color-danger)">*</span>
                    </label>
                    <input wire:model="nombre"
                           type="text"
                           class="input"
                           placeholder="Nombre del cliente"
                           autofocus>
                    @error('nombre')
                        <span style="color:var(--color-danger); font-size:.78rem; margin-top:.2rem; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600;
                                   color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Teléfono
                    </label>
                    <input wire:model="telefono"
                           type="text"
                           class="input"
                           placeholder="Ej: 987654321">
                    @error('telefono')
                        <span style="color:var(--color-danger); font-size:.78rem; margin-top:.2rem; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Notas --}}
                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600;
                                   color:var(--color-text-secondary); margin-bottom:.3rem;">
                        Notas
                    </label>
                    <textarea wire:model="notas"
                              class="input"
                              rows="2"
                              placeholder="Información adicional…"
                              style="resize:vertical;"></textarea>
                    @error('notas')
                        <span style="color:var(--color-danger); font-size:.78rem; margin-top:.2rem; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

            {{-- Footer --}}
            <div style="display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.4rem;">
                <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">
                    Cancelar
                </button>
                <button wire:click="guardar"
                        wire:loading.attr="disabled"
                        class="btn btn-primary">
                    <span wire:loading.remove wire:target="guardar">
                        {{ $editandoId ? 'Guardar cambios' : 'Crear cliente' }}
                    </span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </button>
            </div>

        </div>
    </div>
    @endteleport
    @endif

</div>