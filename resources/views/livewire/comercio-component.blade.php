<div class="animate-fade-in" style="max-width:720px;">
    <h1 class="page-title">🏪 Datos del Comercio</h1>

    @if(session('ok'))
        <div class="alert alert-success" style="margin-bottom:1rem;">✅ {{ session('ok') }}</div>
    @endif

    <div class="card" style="padding:1.75rem;">
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Nombre y Dirección --}}
            <div style="display:flex; gap:.75rem;">
                <div style="flex:1;">
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Nombre del comercio</label>
                    <input wire:model="nombre" class="input" placeholder="Ej: Bodega San Martín">
                    @error('nombre') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
                <div style="flex:1;">
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Dirección</label>
                    <input wire:model="direccion" class="input" placeholder="Av. Principal 123">
                    @error('direccion') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Logo --}}
            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">Logo del comercio</label>
                <div style="display:flex; align-items:center; gap:1rem;">
                    @if($logo)
                        <img src="{{ $logo->temporaryUrl() }}" style="height:64px; border-radius:var(--radius-md); border:2px solid var(--color-turquesa);">
                    @elseif($logo_path)
                        <img src="{{ Storage::url($logo_path) }}" style="height:64px; border-radius:var(--radius-md); border:2px solid var(--color-border);">
                    @endif
                    <input type="file" wire:model="logo" accept="image/*" class="input">
                </div>
                @error('logo') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
            </div>

            <hr style="border-color:var(--color-border);">

            {{-- Apariencia y Precios --}}
            <div>
                <p style="font-size:.85rem; font-weight:700; color:var(--color-text-secondary); margin-bottom:.85rem;">
                    🎨 Apariencia y configuración de precios
                </p>
                <div style="display:flex; gap:1.5rem; flex-wrap:wrap; align-items:flex-start;
                            padding:1rem 1.25rem; background:var(--color-surface-2);
                            border-radius:var(--radius-lg); border:1px solid var(--color-border);">

                    {{-- Color primario --}}
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.5rem;">
                            Color del sistema
                        </label>
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <input wire:model.live="color_primario" type="color"
                                   style="width:52px; height:44px; padding:3px; border-radius:var(--radius-md);
                                          border:1.5px solid var(--color-border); cursor:pointer;">
                            <div>
                                <div style="width:40px; height:40px; border-radius:var(--radius-md);
                                            background:{{ $color_primario }};
                                            box-shadow:0 2px 8px rgba(0,0,0,.15);
                                            border:2px solid var(--color-border);">
                                </div>
                            </div>
                            <div>
                                <p style="font-family:monospace; font-size:.82rem; color:var(--color-text-primary); font-weight:600;">
                                    {{ $color_primario }}
                                </p>
                                <p style="font-size:.72rem; color:var(--color-text-muted);">
                                    Se aplica al siguiente inicio de sesión
                                </p>
                            </div>
                        </div>
                        @error('color_primario') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>

                    <div style="width:1px; background:var(--color-border); align-self:stretch;"></div>

                    {{-- Precio helada --}}
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.5rem;">
                            🧊 Precio adicional por producto helado (S/)
                        </label>
                        <input wire:model="precio_helada" type="number" step="0.50" min="0"
                               class="input" style="width:150px;" placeholder="0.00">
                        <p style="font-size:.72rem; color:var(--color-text-muted); margin-top:.3rem;">
                            Se suma al precio unitario de cada producto marcado como helado.
                        </p>
                        @error('precio_helada') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <hr style="border-color:var(--color-border);">
            <p style="font-size:.85rem; font-weight:700; color:var(--color-text-secondary);">Códigos QR de pago</p>

            <div style="display:flex; gap:1.5rem; flex-wrap:wrap;">
                {{-- Yape QR --}}
                <div style="flex:1; min-width:200px;">
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">📱 QR Yape</label>
                    @if($yape_qr)
                        <img src="{{ $yape_qr->temporaryUrl() }}" style="height:120px; border-radius:var(--radius-md); border:2px solid var(--color-turquesa); display:block; margin-bottom:.5rem;">
                    @elseif($yape_qr_path)
                        <img src="{{ Storage::url($yape_qr_path) }}" style="height:120px; border-radius:var(--radius-md); border:2px solid var(--color-border); display:block; margin-bottom:.5rem;">
                    @endif
                    <input type="file" wire:model="yape_qr" accept="image/*" class="input">
                    @error('yape_qr') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>

                {{-- Plin QR --}}
                <div style="flex:1; min-width:200px;">
                    <label style="display:block; font-size:.82rem; font-weight:600; color:var(--color-text-secondary); margin-bottom:.3rem;">💜 QR Plin</label>
                    @if($plin_qr)
                        <img src="{{ $plin_qr->temporaryUrl() }}" style="height:120px; border-radius:var(--radius-md); border:2px solid #7c3aed; display:block; margin-bottom:.5rem;">
                    @elseif($plin_qr_path)
                        <img src="{{ Storage::url($plin_qr_path) }}" style="height:120px; border-radius:var(--radius-md); border:2px solid var(--color-border); display:block; margin-bottom:.5rem;">
                    @endif
                    <input type="file" wire:model="plin_qr" accept="image/*" class="input">
                    @error('plin_qr') <span style="color:var(--color-danger); font-size:.78rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:.5rem;">
                <button wire:click="guardar" class="btn btn-primary" style="padding:.6rem 2rem;">
                    💾 Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>