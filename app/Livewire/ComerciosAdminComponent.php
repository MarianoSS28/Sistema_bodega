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
    public ?int   $editandoId = null;
    public string $nombre     = '';
    public string $direccion  = '';
    public int    $estado     = 1;
    public $logo    = null;
    public $yape_qr = null;
    public $plin_qr = null;
    public string $logo_path    = '';
    public string $yape_qr_path = '';
    public string $plin_qr_path = '';

    public bool $mostrarFormulario = false;

    protected function rules(): array
    {
        return [
            'nombre'    => 'required|min:2',
            'direccion' => 'required|min:5',
            'logo'      => 'nullable|image|max:2048',
            'yape_qr'   => 'nullable|image|max:2048',
            'plin_qr'   => 'nullable|image|max:2048',
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
            $this->editandoId  = $id;
            $this->nombre      = $c->nombre;
            $this->direccion   = $c->direccion;
            $this->estado      = $c->estado;
            $this->logo_path    = $c->logo_path    ?? '';
            $this->yape_qr_path = $c->yape_qr      ?? '';
            $this->plin_qr_path = $c->plin_qr      ?? '';
        } else {
            $this->reset(['editandoId', 'nombre', 'direccion', 'logo_path', 'yape_qr_path', 'plin_qr_path']);
            $this->estado = 1;
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

        // Actualizar imágenes directamente (el SP solo maneja datos básicos)
        if ($this->editandoId) {
            Comercio::where('id', $this->editandoId)->update([
                'logo_path'            => $logoPath,
                'yape_qr'              => $yapePath,
                'plin_qr'              => $plinPath,
                'usuario_modificacion' => $actor,
                'fecha_modificacion'   => now(),
            ]);
        } else {
            // Obtener el id recién insertado
            $nuevo = Comercio::orderByDesc('id')->first();
            if ($nuevo) {
                $nuevo->update([
                    'logo_path' => $logoPath,
                    'yape_qr'   => $yapePath,
                    'plin_qr'   => $plinPath,
                ]);
            }
        }

        $this->mostrarFormulario = false;
        $this->reset(['editandoId', 'nombre', 'direccion', 'logo', 'yape_qr', 'plin_qr',
                      'logo_path', 'yape_qr_path', 'plin_qr_path']);
        $this->estado = 1;
        session()->flash('ok', 'Comercio guardado.');
    }

    public function desactivar(int $id): void
    {
        DB::statement('EXEC bodega.sp_eliminar_comercio @id=?, @actor=?', [
            $id, Auth::user()->nombre_completo,
        ]);
        session()->flash('ok', 'Comercio desactivado.');
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