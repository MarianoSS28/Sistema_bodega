<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentasComponent extends Component
{
    public string $bufferCodigo       = '';
    public string $codigoManual       = '';
    public bool   $mostrarManual      = false;

    public ?array $productoEncontrado  = null;
    public int    $cantidad            = 1;
    public string $error               = '';
    public array  $carrito             = [];
    public bool   $ventaExitosa        = false;
    public bool   $mostrarModalCobro   = false;
    public string $metodoPago          = 'efectivo';
    public string $efectivoRecibido    = '';
    public array  $heladasCarrito      = [];
    public float  $ultimoVuelto        = 0;
    public string $ultimoMetodoPago    = 'efectivo';
    public float  $ultimoTotal         = 0;
    public int    $ultimasCantItems    = 0;
    public array $carritoExpandido     = [];

    // Precio adicional por helada (cargado desde el comercio)
    public float  $precioHelada        = 0;

    public function mount(): void
    {
        $comercio = DB::selectOne(
            'SELECT precio_helada FROM bodega.comercio WHERE id = ? AND estado = 1',
            [Auth::user()->id_comercio]
        );
        $this->precioHelada = (float)($comercio->precio_helada ?? 0);
    }

    public function buscarPorBuffer(string $codigo): void
    {
        $this->error              = '';
        $this->productoEncontrado = null;
        $codigo                   = trim($codigo);

        if (strlen($codigo) < 2) return;

        $p = Producto::where('codigo_barras', $codigo)
                     ->where('estado', 1)
                     ->where('id_comercio', Auth::user()->id_comercio)
                     ->first();

        if (!$p) {
            $this->error = "Producto no encontrado: {$codigo}";
            return;
        }

        $this->productoEncontrado = $p->toArray();
        $this->cantidad           = 1;
        $this->agregarAlCarrito();
    }

    public function buscarManual(): void
    {
        $this->buscarPorBuffer($this->codigoManual);
        $this->codigoManual  = '';
        $this->mostrarManual = false;
    }

    public function toggleManual(): void
    {
        $this->mostrarManual = !$this->mostrarManual;
        $this->codigoManual  = '';
        $this->resetErrorBag();
    }

    public function agregarAlCarrito(): void
    {
        if (!$this->productoEncontrado) return;

        $id  = $this->productoEncontrado['id'];
        $qty = max(1, (int) $this->cantidad);

        // Verificar stock total disponible
        $enCarrito = collect($this->carrito)->where('id_producto', $id)->sum('cantidad');
        $stockDisp = $this->productoEncontrado['stock'] - $enCarrito;

        if ($qty > $stockDisp) {
            $this->error = "Stock insuficiente. Disponible: {$stockDisp}";
            return;
        }

        // AGRUPAR: si ya existe el producto en carrito, sumar cantidad
        foreach ($this->carrito as &$item) {
            if ($item['id_producto'] === $id) {
                $item['cantidad'] += $qty;
                $item['subtotal']  = $item['cantidad'] * $item['precio_unitario'];
                $this->reset(['productoEncontrado', 'cantidad', 'error']);
                return;
            }
        }

        // Si no existe, agregar nuevo
        $this->carrito[] = [
            'id_producto'     => $id,
            'nombre'          => $this->productoEncontrado['nombre'],
            'precio_unitario' => (float) $this->productoEncontrado['precio'],
            'cantidad'        => $qty,
            'subtotal'        => $qty * (float) $this->productoEncontrado['precio'],
            'foto_path'       => $this->productoEncontrado['foto_path'] ?? '',
        ];

        $this->reset(['productoEncontrado', 'cantidad', 'error']);
    }

    public function abrirCobro(): void
    {
        if (empty($this->carrito)) return;

        // EXPANDIR carrito: cada unidad es un ítem separado en el modal
        $this->carritoExpandido = [];
        foreach ($this->carrito as $item) {
            for ($i = 0; $i < $item['cantidad']; $i++) {
                $this->carritoExpandido[] = [
                    'id_producto'     => $item['id_producto'],
                    'nombre'          => $item['nombre'],
                    'precio_unitario' => $item['precio_unitario'],
                    'cantidad'        => 1,
                    'subtotal'        => $item['precio_unitario'],
                    'foto_path'       => $item['foto_path'],
                ];
            }
        }

        $this->heladasCarrito    = array_fill(0, count($this->carritoExpandido), false);
        $this->metodoPago        = 'efectivo';
        $this->efectivoRecibido  = '';
        $this->mostrarModalCobro = true;
    }

    public function quitarItem(int $index): void
    {
        array_splice($this->carrito, $index, 1);
        array_splice($this->heladasCarrito, $index, 1);
    }

    public function totalCarrito(): float
    {
        $items = $this->mostrarModalCobro ? $this->carritoExpandido : $this->carrito;
        $total = 0;
        foreach ($items as $i => $item) {
            $esHelada = $this->heladasCarrito[$i] ?? false;
            $precio   = $item['precio_unitario'] + ($esHelada ? $this->precioHelada : 0);
            $total   += $precio * $item['cantidad'];
        }
        return $total;
    }

    public function calcularVuelto(): float
    {
        if ($this->metodoPago !== 'efectivo' || $this->efectivoRecibido === '') return 0;
        return max(0, (float) $this->efectivoRecibido - $this->totalCarrito());
    }

    public function registrarVenta(): void
    {
        if (empty($this->carrito)) return;
        if ($this->metodoPago === 'efectivo' && (float) $this->efectivoRecibido < $this->totalCarrito()) return;

        $estacion               = request()->ip();
        $this->ultimoTotal      = $this->totalCarrito();
        $this->ultimasCantItems = array_sum(array_column($this->carrito, 'cantidad'));
        $vuelto                 = $this->calcularVuelto();
        $efectivo               = $this->metodoPago === 'efectivo' ? (float) $this->efectivoRecibido : null;

        DB::transaction(function () use ($estacion, $vuelto, $efectivo) {
            $result = DB::select(
                'EXEC bodega.sp_registrar_venta @total=?, @estacion=?, @metodo_pago=?, @efectivo_recibido=?, @vuelto=?, @id_comercio=?',
                [$this->totalCarrito(), $estacion, $this->metodoPago, $efectivo, $vuelto ?: null, Auth::user()->id_comercio]
            );
            $idVenta = $result[0]->id_venta;

            foreach ($this->carritoExpandido as $i => $item) {
                $esHelada    = ($this->heladasCarrito[$i] ?? false) ? 1 : 0;
                $precioFinal = $item['precio_unitario'] + ($esHelada ? $this->precioHelada : 0);
                DB::statement(
                    'EXEC bodega.sp_registrar_detalle @id_venta=?, @id_producto=?, @cantidad=?, @precio=?, @estacion=?, @es_helada=?',
                    [$idVenta, $item['id_producto'], 1, $precioFinal, $estacion, $esHelada]
                );
            }
        });

        $this->ultimoVuelto      = $vuelto;
        $this->ultimoMetodoPago  = $this->metodoPago;
        $this->carrito           = [];
        $this->heladasCarrito    = [];
        $this->carritoExpandido  = [];
        $this->mostrarModalCobro = false;
        $this->ventaExitosa      = true;
    }

    public function cerrarExito(): void
    {
        $this->ventaExitosa     = false;
        $this->ultimoTotal      = 0;
        $this->ultimasCantItems = 0;
    }

    public function render()
    {
        return view('livewire.ventas-component', [
            'total' => $this->totalCarrito(),
        ])->layout('layouts.ventas');
    }
}