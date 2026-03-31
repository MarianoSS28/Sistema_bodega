<div class="animate-fade-in" style="max-width:560px;">
    <h1 class="page-title">Modo Mantenimiento</h1>

    @if(session('ok'))
    <div class="alert {{ $activo ? 'alert-danger' : 'alert-success' }}" style="margin-bottom:1.25rem;">
        {{ session('ok') }}
    </div>
    @endif

    <div class="card" style="padding:1.75rem;">

        {{-- Toggle principal --}}
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:1.25rem; border-radius:var(--radius-lg);
                    background:{{ $activo ? 'var(--color-danger-light)' : 'var(--color-turquesa-muted)' }};
                    border:2px solid {{ $activo ? 'var(--color-danger)' : 'var(--color-turquesa)' }};
                    margin-bottom:1.5rem;">
            <div>
                <p style="font-weight:700; font-size:1rem;
                           color:{{ $activo ? 'var(--color-danger)' : 'var(--color-turquesa-dark)' }};">
                    Sistema {{ $activo ? 'EN MANTENIMIENTO' : 'OPERATIVO' }}
                </p>
                <p style="font-size:.82rem; color:var(--color-text-muted); margin-top:.2rem;">
                    {{ $activo
                        ? 'Los usuarios no pueden acceder. Solo los administradores pueden usar el sistema.'
                        : 'El sistema esta disponible para todos los usuarios.' }}
                </p>
            </div>

            {{-- Toggle switch --}}
            <button wire:click="toggleMantenimiento"
                    wire:confirm="{{ $activo ? 'Desactivar el modo mantenimiento?' : 'Activar el modo mantenimiento? Los usuarios no podran acceder.' }}"
                    style="
                        position:relative; width:56px; height:28px;
                        border-radius:99px; border:none; cursor:pointer;
                        background:{{ $activo ? 'var(--color-danger)' : 'var(--color-border)' }};
                        transition:background var(--transition-base);
                        flex-shrink:0;
                    ">
                <span style="
                    position:absolute; top:3px;
                    left:{{ $activo ? '31px' : '3px' }};
                    width:22px; height:22px;
                    background:#fff; border-radius:50%;
                    transition:left var(--transition-base);
                    box-shadow:0 1px 4px rgba(0,0,0,.2);
                "></span>
            </button>
        </div>

        {{-- Mensaje personalizado --}}
        <div style="margin-bottom:1.25rem;">
            <label style="display:block; font-size:.82rem; font-weight:600;
                           color:var(--color-text-secondary); margin-bottom:.4rem;">
                Mensaje para los usuarios
            </label>
            <textarea wire:model="mensaje"
                      class="input"
                      rows="3"
                      placeholder="Ej: Sistema en mantenimiento programado. Vuelve en 30 minutos."
                      style="resize:vertical;"></textarea>
            <p style="font-size:.75rem; color:var(--color-text-muted); margin-top:.3rem;">
                Este mensaje se mostrara en la pantalla de mantenimiento.
            </p>
        </div>

        <div style="display:flex; justify-content:flex-end;">
            <button wire:click="guardar" class="btn btn-primary">Guardar cambios</button>
        </div>
    </div>

    {{-- Info --}}
    <div style="margin-top:1.25rem; padding:1rem 1.25rem; border-radius:var(--radius-md);
                background:var(--color-celeste-muted); border:1px solid var(--color-celeste-dark);">
        <p style="font-size:.82rem; color:var(--color-celeste-dark); font-weight:600; margin-bottom:.4rem;">
            Como funciona
        </p>
        <ul style="font-size:.8rem; color:var(--color-text-secondary); padding-left:1rem; line-height:1.8;">
            <li>Al activar el mantenimiento, cualquier usuario que intente acceder vera la pantalla de aviso.</li>
            <li>Los administradores (rol 1) pueden seguir usando el sistema con normalidad.</li>
            <li>El login sigue accesible para que los administradores puedan entrar.</li>
        </ul>
    </div>
</div>