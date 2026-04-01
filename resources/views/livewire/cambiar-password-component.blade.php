<div class="modal-box animate-scale-in" style="max-width:420px; width:100%;">

    {{-- Ícono y título --}}
    <div style="text-align:center; margin-bottom:1.5rem;">
        <div style="
            width:72px; height:72px;
            background:var(--gradient-brand);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:1.9rem;
            margin:0 auto .75rem;
            box-shadow:0 0 0 10px rgba(39,184,109,.12);
        ">🔑</div>

        <h1 style="font-size:1.25rem; font-weight:800; color:var(--color-text-primary); margin-bottom:.3rem;">
            Cambiar contraseña
        </h1>
        <p style="font-size:.85rem; color:var(--color-text-muted); line-height:1.5;">
            Por seguridad debes establecer una nueva contraseña antes de continuar.
        </p>
    </div>

    {{-- Errores de validación --}}
    @if($error)
        <div class="alert alert-danger" style="margin-bottom:1rem;">{{ $error }}</div>
    @endif

    <div style="display:flex; flex-direction:column; gap:.85rem;">

        {{-- Nueva contraseña --}}
        <div>
            <label style="display:block; font-size:.82rem; font-weight:600;
                           color:var(--color-text-secondary); margin-bottom:.3rem;">
                Nueva contraseña
            </label>
            <input wire:model="password_nuevo"
                   type="password"
                   class="input"
                   placeholder="Mínimo 6 caracteres"
                   wire:keydown.enter="guardar">
            @error('password_nuevo')
                <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div>
            <label style="display:block; font-size:.82rem; font-weight:600;
                           color:var(--color-text-secondary); margin-bottom:.3rem;">
                Confirmar contraseña
            </label>
            <input wire:model="password_confirm"
                   type="password"
                   class="input"
                   placeholder="Repite la nueva contraseña"
                   wire:keydown.enter="guardar">
            @error('password_confirm')
                <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span>
            @enderror
        </div>

        {{-- Indicador de coincidencia en tiempo real --}}
        @if($password_nuevo && $password_confirm)
            @if($password_nuevo === $password_confirm)
                <div style="display:flex; align-items:center; gap:.4rem;
                             font-size:.78rem; color:var(--color-turquesa); font-weight:600;">
                    ✅ Las contraseñas coinciden
                </div>
            @else
                <div style="display:flex; align-items:center; gap:.4rem;
                             font-size:.78rem; color:var(--color-danger); font-weight:600;">
                    ❌ Las contraseñas no coinciden
                </div>
            @endif
        @endif

        {{-- Botón guardar --}}
        <button wire:click="guardar"
                wire:loading.attr="disabled"
                class="btn btn-primary"
                style="width:100%; justify-content:center; padding:.65rem; margin-top:.25rem;">
            <span wire:loading.remove>Guardar nueva contraseña</span>
            <span wire:loading>Guardando...</span>
        </button>

    </div>

    <p style="text-align:center; font-size:.75rem; color:var(--color-text-muted); margin-top:1rem;">
        Esta acción es obligatoria y no puede omitirse.
    </p>
</div>