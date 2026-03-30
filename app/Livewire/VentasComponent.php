<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentasComponent extends Component
{
    public string $codigoBusqueda    = '';
    public ?array $productoEncontrado = null;
    public int    $cantidad = 1;
    public string $error    = '';
    public array  $carrito  = [];
    public bool   $ventaExitosa = false;
    public bool   $mostrarModalCobro = false;
    public string $metodoPago        = 'efectivo';
    public string $efectivoRecibido  = '';
    public array  $heladasCarrito    = []; 
    public float  $ultimoVuelto     = 0;
    public string $ultimoMetodoPago = 'efectivo';  
    public string $bufferTeclas = ''; 

    // Para la confirmación visual
    public float  $ultimoTotal = 0;
    public int    $ultimasCantItems = 0;

    // Auto-búsqueda con debounce: se dispara desde la vista con wire:model.live.debounce.400ms
    public function updatedCodigoBusqueda(): void
    {
        $this->error = '';
        $this->productoEncontrado = null;

        $codigo = trim($this->codigoBusqueda);
        if (strlen($codigo) < 2) return;

        $p = Producto::where('codigo_barras', $codigo)
                     ->where('estado', 1)
                     ->where('id_comercio', Auth::user()->id_comercio)
                     ->first();

        if (!$p) {
            // Solo mostrar error si parece un código completo (más de 5 chars)
            if (strlen($codigo) >= 5) {
                $this->error = 'Producto no encontrado.';
                $this->codigoBusqueda = '';
            }
            return;
        }

        $this->productoEncontrado = $p->toArray();
        $this->cantidad = 1;
        $this->agregarAlCarrito();
    }

    public function updatedBufferTeclas(): void
    {
        // Se llama con wire:model.live sin debounce — el JS acumula y envía
        // Ver cambio en la vista
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
            $this->codigoBusqueda = '';
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

    public function abrirCobro(): void
    {
        if (empty($this->carrito)) return;
        // Inicializar heladas en false para cada ítem
        $this->heladasCarrito = array_fill(0, count($this->carrito), false);
        $this->metodoPago = 'efectivo';
        $this->efectivoRecibido = '';
        $this->mostrarModalCobro = true;
    }

    public function calcularVuelto(): float
    {
        if ($this->metodoPago !== 'efectivo' || $this->efectivoRecibido === '') return 0;
        return max(0, (float) $this->efectivoRecibido - $this->totalCarrito());
    }

    public function registrarVenta(): void
    {
        if (empty($this->carrito)) return;
        if ($this->metodoPago === 'efectivo' && (float)$this->efectivoRecibido < $this->totalCarrito()) {
            return; // No alcanza
        }

        $estacion          = request()->ip();
        $this->ultimoTotal      = $this->totalCarrito();
        $this->ultimasCantItems = array_sum(array_column($this->carrito, 'cantidad'));
        $vuelto            = $this->calcularVuelto();
        $efectivo          = $this->metodoPago === 'efectivo' ? (float)$this->efectivoRecibido : null;

        DB::transaction(function () use ($estacion, $vuelto, $efectivo) {
            $result = DB::select('EXEC bodega.sp_registrar_venta @total = ?, @estacion = ?, @metodo_pago = ?, @efectivo_recibido = ?, @vuelto = ?, @id_comercio=?', [
                $this->totalCarrito(), $estacion, $this->metodoPago, $efectivo, $vuelto ?: null, Auth::user()->id_comercio,
            ]);
            $idVenta = $result[0]->id_venta;

            foreach ($this->carrito as $i => $item) {
                $esHelada = $this->heladasCarrito[$i] ?? false;
                DB::statement('EXEC bodega.sp_registrar_detalle @id_venta = ?, @id_producto = ?, @cantidad = ?, @precio = ?, @estacion = ?, @es_helada = ?', [
                    $idVenta, $item['id_producto'], $item['cantidad'], $item['precio_unitario'], $estacion, $esHelada ? 1 : 0,
                ]);
            }
        });

        $this->ultimoVuelto    = $vuelto;
        $this->ultimoMetodoPago = $this->metodoPago;
        $this->carrito         = [];
        $this->heladasCarrito  = [];
        $this->mostrarModalCobro = false;
        $this->ventaExitosa    = true;
    }

    public function cerrarExito(): void
    {
        $this->ventaExitosa = false;
        $this->ultimoTotal = 0;
        $this->ultimasCantItems = 0;
    }

    public function render()
    {
        return view('livewire.ventas-component', [
            'total' => $this->totalCarrito(),
        ])->layout('layouts.app');
    }
}