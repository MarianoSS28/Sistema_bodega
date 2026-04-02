<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Oferta;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OfertasComponent extends Component
{
    use WithPagination;

    public string $busqueda = '';

    // Form
    public ?int   $editandoId      = null;
    public string $nombre          = '';
    public string $descripcion     = '';
    public string $tipo            = 'porcentaje';
    public string $valor           = '0';
    public string $cantidad_paga   = '2';
    public string $cantidad_lleva  = '3';
    public string $fecha_inicio    = '';
    public string $fecha_fin       = '';
    public array  $productosSeleccionados = [];
    public bool   $mostrarFormulario = false;

    public function updatedBusqueda(): void { $this->resetPage(); }

    protected function rules(): array
    {
        return [
            'nombre'      => 'required|min:3|max:200',
            'descripcion' => 'nullable|max:500',
            'tipo'        => 'required|in:2x1,nxm,porcentaje,fijo',
            'valor'       => 'required|numeric|min:0',
            'cantidad_paga'  => 'nullable|integer|min:1',
            'cantidad_lleva' => 'nullable|integer|min:1',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date',
        ];
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->resetErrorBag();
        if ($id) {
            $o = Oferta::findOrFail($id);
            $this->editandoId     = $id;
            $this->nombre         = $o->nombre;
            $this->descripcion    = $o->descripcion ?? '';
            $this->tipo           = $o->tipo;
            $this->valor          = (string) $o->valor;
            $this->cantidad_paga  = (string) ($o->cantidad_paga  ?? 2);
            $this->cantidad_lleva = (string) ($o->cantidad_lleva ?? 3);
            $this->fecha_inicio   = $o->fecha_inicio ?? '';
            $this->fecha_fin      = $o->fecha_fin    ?? '';
            $this->productosSeleccionados = $o->productos()
                ->pluck('bodega.productos.id')
                ->map(fn($v) => (string) $v)
                ->toArray();
        } else {
            $this->reset([
                'editandoId', 'nombre', 'descripcion', 'valor',
                'fecha_inicio', 'fecha_fin', 'productosSeleccionados',
            ]);
            $this->tipo           = 'porcentaje';
            $this->cantidad_paga  = '2';
            $this->cantidad_lleva = '3';
        }
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();
        $actor      = Auth::user()->nombre_completo;
        $idComercio = Auth::user()->id_comercio;

        $result = DB::select('EXEC bodega.sp_guardar_oferta @id=?, @nombre=?, @descripcion=?, @tipo=?,
            @valor=?, @cantidad_paga=?, @cantidad_lleva=?, @fecha_inicio=?, @fecha_fin=?,
            @id_comercio=?, @actor=?', [
            $this->editandoId ?: null,
            $this->nombre,
            $this->descripcion ?: null,
            $this->tipo,
            $this->valor,
            in_array($this->tipo, ['nxm']) ? $this->cantidad_paga  : null,
            in_array($this->tipo, ['nxm']) ? $this->cantidad_lleva : null,
            $this->fecha_inicio ?: null,
            $this->fecha_fin    ?: null,
            $idComercio,
            $actor,
        ]);

        $idOferta = $result[0]->id_oferta ?? $this->editandoId;

        // Sincronizar productos
        $csvIds = implode(',', $this->productosSeleccionados);
        DB::statement('EXEC bodega.sp_sync_oferta_productos @id_oferta=?, @ids_productos=?', [
            $idOferta, $csvIds ?: '',
        ]);

        $this->mostrarFormulario = false;
        $this->reset(['editandoId', 'nombre', 'descripcion', 'valor',
                      'fecha_inicio', 'fecha_fin', 'productosSeleccionados']);
        $this->tipo          = 'porcentaje';
        $this->cantidad_paga = '2'; $this->cantidad_lleva = '3';
        session()->flash('ok', 'Oferta guardada.');
    }

    public function eliminar(int $id): void
    {
        DB::statement('EXEC bodega.sp_eliminar_oferta @id=?, @actor=?', [
            $id, Auth::user()->nombre_completo,
        ]);
        session()->flash('ok', 'Oferta eliminada.');
    }

    public function toggleActiva(int $id): void
    {
        DB::statement('EXEC bodega.sp_toggle_oferta @id=?, @actor=?', [
            $id, Auth::user()->nombre_completo,
        ]);
    }

    public function render()
    {
        $ofertas = Oferta::with('productos')
            ->where('estado', 1)
            ->where('id_comercio', Auth::user()->id_comercio)
            ->when($this->busqueda, fn($q) =>
                $q->where('nombre', 'like', "%{$this->busqueda}%")
            )
            ->orderByDesc('id')
            ->paginate(10);

        $todosProductos = Producto::where('estado', 1)
            ->where('id_comercio', Auth::user()->id_comercio)
            ->orderBy('nombre')
            ->get();

        return view('livewire.ofertas-component', compact('ofertas', 'todosProductos'))
            ->layout('layouts.app');
    }
}