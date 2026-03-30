<div class="modal-box animate-scale-in" style="max-width:380px; width:100%;">
    <div style="text-align:center; margin-bottom:1.5rem;">
        <div style="font-size:2.5rem;">🏪</div>
        <h1 style="font-size:1.4rem; font-weight:800; color:var(--color-text-primary);">Sistema Bodega</h1>
        <p style="font-size:.85rem; color:var(--color-text-muted);">Ingresa con tu DNI y contraseña</p>
    </div>

    @if($error)
        <div class="alert alert-danger" style="margin-bottom:1rem;">{{ $error }}</div>
    @endif

    <div style="display:flex; flex-direction:column; gap:.85rem;">
        <div>
            <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">DNI</label>
            <input wire:model="dni" wire:keydown.enter="login" class="input" placeholder="Ingresa tu DNI" maxlength="11">
        </div>
        <div>
            <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Contraseña</label>
            <input wire:model="password" wire:keydown.enter="login" type="password" class="input" placeholder="Contraseña">
        </div>
        <button wire:click="login" class="btn btn-primary" style="width:100%; justify-content:center; padding:.65rem;">
            Ingresar
        </button>
    </div>
</div>