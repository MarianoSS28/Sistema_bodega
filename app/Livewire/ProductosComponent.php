<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductosComponent extends Component
{
    use WithFileUploads;

    public string $busqueda = '';

    public ?int   $editandoId    = null;
    public string $nombre        = '';
    public string $codigo_barras = '';
    public string $precio        = '';
    public string $stock         = '';
    public $foto = null;
    public string $fotoActual = '';

    public bool $mostrarFormulario = false;

    protected function rules(): array
    {
        return [
            'nombre'        => 'required|min:2',
            'codigo_barras' => 'required',
            'precio'        => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'foto'          => 'nullable|image|max:2048',
        ];
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->foto = null;

        if ($id) {
            $p = Producto::findOrFail($id);
            $this->editandoId    = $id;
            $this->nombre        = $p->nombre;
            $this->codigo_barras = $p->codigo_barras;
            $this->precio        = $p->precio;
            $this->stock         = $p->stock;
            $this->fotoActual    = $p->foto_path ?? '';
        } else {
            $this->reset(['editandoId', 'nombre', 'codigo_barras', 'precio', 'stock', 'fotoActual']);
        }
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();
        $estacion  = request()->ip();
        $foto_path = $this->fotoActual;

        if ($this->foto) {
            // Borrar foto anterior si existe
            if ($this->fotoActual && Storage::disk('public')->exists($this->fotoActual)) {
                Storage::disk('public')->delete($this->fotoActual);
            }
            $foto_path = $this->foto->store('productos', 'public');
        }

        if ($this->editandoId) {
            DB::statement('EXEC bodega.sp_ActualizarProducto ?, ?, ?, ?, ?, ?, ?', [
                $this->editandoId,
                $this->nombre,
                $this->codigo_barras,
                $this->precio,
                $this->stock,
                $foto_path,
                $estacion,
            ]);
            session()->flash('ok', 'Producto actualizado.');
        } else {
            DB::statement('EXEC bodega.sp_InsertarProducto ?, ?, ?, ?, ?, ?', [
                $this->nombre,
                $this->codigo_barras,
                $this->precio,
                $this->stock,
                $foto_path,
                $estacion,
            ]);
            session()->flash('ok', 'Producto creado.');
        }

        $this->mostrarFormulario = false;
        $this->reset(['editandoId', 'nombre', 'codigo_barras', 'precio', 'stock', 'foto', 'fotoActual']);
    }

    public function desactivar(int $id): void
    {
        Producto::where('id', $id)->update([
            'estado'                => 0,
            'estacion_modificacion' => request()->ip(),
            'fecha_modificacion'    => now(),
        ]);
        session()->flash('ok', 'Producto desactivado.');
    }

    public function render()
    {
        $productos = Producto::where('estado', 1)
            ->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->busqueda}%")
                  ->orWhere('codigo_barras', 'like', "%{$this->busqueda}%");
            })
            ->orderBy('nombre')
            ->get();

        return view('livewire.productos-component', compact('productos'))
            ->layout('layouts.app');
    }
}