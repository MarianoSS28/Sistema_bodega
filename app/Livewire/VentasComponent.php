<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class VentasComponent extends Component
{
    public string $codigoBusqueda   = '';
    public ?array $productoEncontrado = null;
    public int    $cantidad = 1;
    public string $error    = '';
    public array  $carrito  = [];

    public function buscarProducto(): void
    {
        $this->error = '';
        $this->productoEncontrado = null;

        $p = Producto::where('codigo_barras', trim($this->codigoBusqueda))
                     ->where('estado', 1)
                     ->first();

        if (!$p) {
            $this->error = 'Producto no encontrado.';
            return;
        }

        $this->productoEncontrado = $p->toArray();
        $this->cantidad = 1;

        // Auto-agregar al carrito
        $this->agregarAlCarrito();
    }

    public function agregarAlCarrito(): void
    {
        if (!$this->productoEncontrado) return;

        $id  = $this->productoEncontrado['id'];
        $qty = max(1, (int) $this->cantidad);

        $enCarrito = collect($this->carrito)->where('id_producto', $id)->sum('cantidad');
        $stockDisp = $this->productoEncontrado['stock'] - $enCarrito;

        if ($qty > $stockDisp) {
            $this->error = "Stock insuficiente. Disponible: {$stockDisp}";
            return;
        }

        foreach ($this->carrito as &$item) {
            if ($item['id_producto'] === $id) {
                $item['cantidad'] += $qty;
                $item['subtotal']  = $item['cantidad'] * $item['precio_unitario'];
                $this->reset(['codigoBusqueda', 'productoEncontrado', 'cantidad', 'error']);
                return;
            }
        }

        $this->carrito[] = [
            'id_producto'     => $id,
            'nombre'          => $this->productoEncontrado['nombre'],
            'precio_unitario' => (float) $this->productoEncontrado['precio'],
            'cantidad'        => $qty,
            'subtotal'        => $qty * (float) $this->productoEncontrado['precio'],
            'foto_path'       => $this->productoEncontrado['foto_path'] ?? '',
        ];

        $this->reset(['codigoBusqueda', 'productoEncontrado', 'cantidad', 'error']);
    }

    public function quitarItem(int $index): void
    {
        array_splice($this->carrito, $index, 1);
    }

    public function totalCarrito(): float
    {
        return collect($this->carrito)->sum('subtotal');
    }

    public function registrarVenta(): void
    {
        if (empty($this->carrito)) return;

        $estacion = request()->ip();

        DB::transaction(function () use ($estacion) {
            $result  = DB::select('EXEC bodega.sp_registrar_venta @total = ?, @estacion = ?', [
                $this->totalCarrito(), $estacion
            ]);
            $idVenta = $result[0]->id_venta;

            foreach ($this->carrito as $item) {
                DB::statement('EXEC bodega.sp_registrar_detalle @id_venta = ?, @id_producto = ?, @cantidad = ?, @precio = ?, @estacion = ?', [
                    $idVenta,
                    $item['id_producto'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $estacion,
                ]);
            }
        });

        $this->carrito = [];
        session()->flash('ok', 'Venta registrada correctamente.');
    }

    public function render()
    {
        return view('livewire.ventas-component', [
            'total' => $this->totalCarrito(),
        ])->layout('layouts.app');
    }
}