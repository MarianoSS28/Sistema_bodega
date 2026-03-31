<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Comercio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComercioComponent extends Component
{
    use WithFileUploads;

    public ?int   $id        = null;
    public string $nombre    = '';
    public string $direccion = '';
    public $logo     = null;
    public $yape_qr  = null;
    public $plin_qr  = null;
    public string $logo_path    = '';
    public string $yape_qr_path = '';
    public string $plin_qr_path = '';

    // Nuevos campos
    public string $color_primario = '#27B86D';
    public string $precio_helada  = '0';

    public function mount(): void
    {
        $comercio = Comercio::where('estado', 1)->where('id', Auth::user()->id_comercio)->first();
        if ($comercio) {
            $this->id             = $comercio->id;
            $this->nombre         = $comercio->nombre;
            $this->direccion      = $comercio->direccion;
            $this->logo_path      = $comercio->logo_path    ?? '';
            $this->yape_qr_path   = $comercio->yape_qr      ?? '';
            $this->plin_qr_path   = $comercio->plin_qr      ?? '';
            $this->color_primario = $comercio->color_primario ?? '#27B86D';
            $this->precio_helada  = (string)($comercio->precio_helada ?? '0');
        }
    }

    protected function rules(): array
    {
        return [
            'nombre'         => 'required|min:2',
            'direccion'      => 'required|min:5',
            'logo'           => 'nullable|image|max:2048',
            'yape_qr'        => 'nullable|image|max:2048',
            'plin_qr'        => 'nullable|image|max:2048',
            'color_primario' => 'nullable|max:7',
            'precio_helada'  => 'nullable|numeric|min:0',
        ];
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

        $data = [
            'nombre'               => $this->nombre,
            'direccion'            => $this->direccion,
            'logo_path'            => $logoPath,
            'yape_qr'              => $yapePath,
            'plin_qr'              => $plinPath,
            'color_primario'       => $this->color_primario,
            'precio_helada'        => $this->precio_helada ?: 0,
            'usuario_modificacion' => $actor,
            'fecha_modificacion'   => now(),
        ];

        if ($this->id) {
            Comercio::where('id', $this->id)->update($data);
        } else {
            $data['estado']           = 1;
            $data['usuario_creacion'] = $actor;
            $data['fecha_creacion']   = now();
            $this->id = Comercio::insertGetId($data);
        }

        $this->logo_path      = $logoPath;
        $this->yape_qr_path   = $yapePath;
        $this->plin_qr_path   = $plinPath;
        $this->logo = $this->yape_qr = $this->plin_qr = null;

        session()->flash('ok', 'Datos del comercio guardados.');
    }

    public function render()
    {
        return view('livewire.comercio-component')->layout('layouts.app');
    }
}