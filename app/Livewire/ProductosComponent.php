<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class ProductosComponent extends Component
{
    // Lista / búsqueda
    public string $busqueda = '';

    // Formulario
    public ?int   $editandoId    = null;
    public string $nombre        = '';
    public string $codigo_barras = '';
    public string $precio        = '';
    public string $stock         = '';

    public bool $mostrarFormulario = false;

    protected function rules(): array
    {
        return [
            'nombre'        => 'required|min:2',
            'codigo_barras' => 'required',
            'precio'        => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
        ];
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->resetErrorBag();
        if ($id) {
            $p = Producto::findOrFail($id);
            $this->editandoId    = $id;
            $this->nombre        = $p->nombre;
            $this->codigo_barras = $p->codigo_barras;
            $this->precio        = $p->precio;
            $this->stock         = $p->stock;
        } else {
            $this->reset(['editandoId', 'nombre', 'codigo_barras', 'precio', 'stock']);
        }
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();
        $estacion = request()->ip();

        if ($this->editandoId) {
            DB::statement('EXEC bodega.sp_ActualizarProducto ?, ?, ?, ?, ?, ?', [
                $this->editandoId,
                $this->nombre,
                $this->codigo_barras,
                $this->precio,
                $this->stock,
                $estacion,
            ]);
            session()->flash('ok', 'Producto actualizado.');
        } else {
            DB::statement('EXEC bodega.sp_InsertarProducto ?, ?, ?, ?, ?', [
                $this->nombre,
                $this->codigo_barras,
                $this->precio,
                $this->stock,
                $estacion,
            ]);
            session()->flash('ok', 'Producto creado.');
        }

        $this->mostrarFormulario = false;
        $this->reset(['editandoId', 'nombre', 'codigo_barras', 'precio', 'stock']);
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
