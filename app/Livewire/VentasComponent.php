<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentasComponent extends Component
{
    // Buffer acumulado desde JS (teclas del lector de barras o teclado)
    public string $bufferCodigo      = '';
    // Input manual visible solo cuando el usuario pulsa el botón
    public string $codigoManual      = '';
    public bool   $mostrarManual     = false;

    public ?array $productoEncontrado = null;
    public int    $cantidad           = 1;
    public string $error              = '';
    public array  $carrito            = [];
    public bool   $ventaExitosa       = false;
    public bool   $mostrarModalCobro  = false;
    public string $metodoPago         = 'efectivo';
    public string $efectivoRecibido   = '';
    public array  $heladasCarrito     = [];
    public float  $ultimoVuelto       = 0;
    public string $ultimoMetodoPago   = 'efectivo';
    public float  $ultimoTotal        = 0;
    public int    $ultimasCantItems   = 0;

    /**
     * Llamado desde JS cuando el buffer acumuló un código completo
     * (enter del lector o pausa de 300 ms con ≥ 3 chars)
     */
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

    /**
     * Llamado desde el input manual visible
     */
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
                $this->reset(['productoEncontrado', 'cantidad', 'error']);
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

        $this->reset(['productoEncontrado', 'cantidad', 'error']);
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
        $this->heladasCarrito    = array_fill(0, count($this->carrito), false);
        $this->metodoPago        = 'efectivo';
        $this->efectivoRecibido  = '';
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

            foreach ($this->carrito as $i => $item) {
                DB::statement(
                    'EXEC bodega.sp_registrar_detalle @id_venta=?, @id_producto=?, @cantidad=?, @precio=?, @estacion=?, @es_helada=?',
                    [$idVenta, $item['id_producto'], $item['cantidad'], $item['precio_unitario'], $estacion, ($this->heladasCarrito[$i] ?? false) ? 1 : 0]
                );
            }
        });

        $this->ultimoVuelto     = $vuelto;
        $this->ultimoMetodoPago = $this->metodoPago;
        $this->carrito          = [];
        $this->heladasCarrito   = [];
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
        ])->layout('layouts.ventas');   // <-- layout independiente
    }
}