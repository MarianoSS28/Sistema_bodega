<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Comercio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ComerciosAdminComponent extends Component
{
    use WithPagination, WithFileUploads;

    public string $busqueda = '';

    // Form
    public ?int   $editandoId    = null;
    public string $nombre        = '';
    public string $direccion     = '';
    public int    $estado        = 1;
    public $logo    = null;
    public $yape_qr = null;
    public $plin_qr = null;
    public string $logo_path    = '';
    public string $yape_qr_path = '';
    public string $plin_qr_path = '';

    // Nuevos campos
    public int    $bloqueado       = 0;
    public string $motivo_bloqueo  = '';
    public string $precio_helada   = '0';
    public string $color_primario  = '#27B86D';

    public bool $mostrarFormulario = false;

    protected function rules(): array
    {
        return [
            'nombre'         => 'required|min:2',
            'direccion'      => 'required|min:5',
            'logo'           => 'nullable|image|max:2048',
            'yape_qr'        => 'nullable|image|max:2048',
            'plin_qr'        => 'nullable|image|max:2048',
            'precio_helada'  => 'nullable|numeric|min:0',
            'color_primario' => 'nullable|max:7',
            'motivo_bloqueo' => 'nullable|max:500',
        ];
    }

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->logo = $this->yape_qr = $this->plin_qr = null;

        if ($id) {
            $c = Comercio::findOrFail($id);
            $this->editandoId     = $id;
            $this->nombre         = $c->nombre;
            $this->direccion      = $c->direccion;
            $this->estado         = $c->estado;
            $this->logo_path      = $c->logo_path    ?? '';
            $this->yape_qr_path   = $c->yape_qr      ?? '';
            $this->plin_qr_path   = $c->plin_qr      ?? '';
            $this->bloqueado      = (int)($c->bloqueado ?? 0);
            $this->motivo_bloqueo = $c->motivo_bloqueo ?? '';
            $this->precio_helada  = (string)($c->precio_helada ?? '0');
            $this->color_primario = $c->color_primario ?? '#27B86D';
        } else {
            $this->reset(['editandoId', 'nombre', 'direccion', 'logo_path', 'yape_qr_path', 'plin_qr_path',
                          'bloqueado', 'motivo_bloqueo', 'precio_helada']);
            $this->estado         = 1;
            $this->color_primario = '#27B86D';
        }
        $this->mostrarFormulario = true;
    }

    private function subirImagen($file, string $pathActual, string $folder): string
    {
        if (!$file) return $pathActual;
        if ($pathActual && Storage::disk('public')->exists($pathActual)) {
            Storage::disk('public')->delete($pathActual);
        }
        return $file->store($folder, 'public');
    }

    public function guardar(): void
    {
        $this->validate();
        $actor = Auth::user()->nombre_completo;

        $logoPath = $this->subirImagen($this->logo,    $this->logo_path,    'comercio/logos');
        $yapePath = $this->subirImagen($this->yape_qr, $this->yape_qr_path, 'comercio/qr');
        $plinPath = $this->subirImagen($this->plin_qr, $this->plin_qr_path, 'comercio/qr');

        DB::statement('EXEC bodega.sp_guardar_comercio @id=?, @nombre=?, @direccion=?, @estado=?, @actor=?', [
            $this->editandoId, $this->nombre, $this->direccion, $this->estado, $actor,
        ]);

        $camposExtra = [
            'logo_path'            => $logoPath,
            'yape_qr'              => $yapePath,
            'plin_qr'              => $plinPath,
            'bloqueado'            => $this->bloqueado,
            'motivo_bloqueo'       => $this->bloqueado ? $this->motivo_bloqueo : null,
            'precio_helada'        => $this->precio_helada ?: 0,
            'color_primario'       => $this->color_primario,
            'usuario_modificacion' => $actor,
            'fecha_modificacion'   => now(),
        ];

        if ($this->editandoId) {
            Comercio::where('id', $this->editandoId)->update($camposExtra);
        } else {
            $nuevo = Comercio::orderByDesc('id')->first();
            if ($nuevo) {
                $nuevo->update($camposExtra);
            }
        }

        $this->mostrarFormulario = false;
        $this->reset(['editandoId', 'nombre', 'direccion', 'logo', 'yape_qr', 'plin_qr',
                      'logo_path', 'yape_qr_path', 'plin_qr_path', 'bloqueado', 'motivo_bloqueo', 'precio_helada']);
        $this->estado         = 1;
        $this->color_primario = '#27B86D';
        session()->flash('ok', 'Comercio guardado.');
    }

    public function desactivar(int $id): void
    {
        DB::statement('EXEC bodega.sp_eliminar_comercio @id=?, @actor=?', [
            $id, Auth::user()->nombre_completo,
        ]);
        session()->flash('ok', 'Comercio desactivado.');
    }

    public function toggleBloqueo(int $id): void
    {
        $c = Comercio::findOrFail($id);
        Comercio::where('id', $id)->update([
            'bloqueado'            => $c->bloqueado ? 0 : 1,
            'usuario_modificacion' => Auth::user()->nombre_completo,
            'fecha_modificacion'   => now(),
        ]);
        session()->flash('ok', $c->bloqueado ? 'Comercio desbloqueado.' : 'Comercio bloqueado.');
    }

    public function render()
    {
        $comercios = Comercio::when($this->busqueda, fn($q) =>
                $q->where('nombre', 'like', "%{$this->busqueda}%")
                  ->orWhere('direccion', 'like', "%{$this->busqueda}%")
            )
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.comercios-admin-component', compact('comercios'))
            ->layout('layouts.app');
    }
}