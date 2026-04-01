<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Fiado;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FiadosComponent extends Component
{
    use WithPagination;

    // Filtros
    public string $busqueda    = '';
    public string $filtroEstado = '1'; // 1=activos por defecto

    // Modal pago
    public ?int   $fiadoPagandoId = null;
    public string $montoPago      = '';
    public string $metodoPagoPago = 'efectivo';
    public string $notasPago      = '';

    // Detalle expandido
    public ?int $fiadoDetalleId = null;
    public array $detalleProductos = [];
    public array $detallePagos     = [];

    public function updatedBusqueda(): void { $this->resetPage(); }
    public function updatedFiltroEstado(): void { $this->resetPage(); }

    public function verDetalle(int $id): void {
        if ($this->fiadoDetalleId === $id) {
            $this->fiadoDetalleId = null;
            return;
        }
        $this->fiadoDetalleId = $id;
        $rows = DB::select('EXEC bodega.sp_detalle_fiado @id_fiado=?', [$id]);
        // sp devuelve 2 result sets; con DB::select solo llega el primero
        // usamos consultas directas:
        $this->detalleProductos = DB::select(
            'SELECT fd.id, p.nombre AS producto, fd.cantidad, fd.precio_unitario, fd.subtotal, fd.es_helada
             FROM bodega.fiado_detalle fd
             INNER JOIN bodega.productos p ON p.id = fd.id_producto
             WHERE fd.id_fiado = ? AND fd.estado = 1', [$id]
        );
        $this->detallePagos = DB::select(
            'SELECT id, monto, metodo_pago, notas, fecha_creacion
             FROM bodega.fiado_pagos WHERE id_fiado = ? AND estado = 1
             ORDER BY fecha_creacion ASC', [$id]
        );
    }

    public function abrirPago(int $id): void {
        $this->fiadoPagandoId = $id;
        $this->montoPago      = '';
        $this->metodoPagoPago = 'efectivo';
        $this->notasPago      = '';
        $this->resetErrorBag();
    }

    public function registrarPago(): void {
        $this->validate([
            'montoPago' => 'required|numeric|min:0.01',
        ]);

        $fiado = Fiado::findOrFail($this->fiadoPagandoId);

        if ((float)$this->montoPago > $fiado->saldo) {
            $this->addError('montoPago', 'El monto supera el saldo pendiente (S/ '.number_format($fiado->saldo,2).').');
            return;
        }

        DB::statement('EXEC bodega.sp_pagar_fiado @id_fiado=?, @monto=?, @metodo_pago=?, @notas=?, @estacion=?, @actor=?', [
            $this->fiadoPagandoId,
            $this->montoPago,
            $this->metodoPagoPago,
            $this->notasPago ?: null,
            request()->ip(),
            Auth::user()->nombre_completo,
        ]);

        $this->fiadoPagandoId = null;
        // refrescar detalle si estaba abierto
        if ($this->fiadoDetalleId) {
            $this->verDetalle($this->fiadoDetalleId);
        }
        session()->flash('ok', 'Pago registrado.');
    }

    public function cancelarFiado(int $id): void {
        DB::statement('EXEC bodega.sp_cancelar_fiado @id_fiado=?, @actor=?', [
            $id, Auth::user()->nombre_completo,
        ]);
        session()->flash('ok', 'Fiado cancelado.');
    }

    public function render() {
        $query = Fiado::with('cliente')
            ->where('bodega.fiados.id_comercio', Auth::user()->id_comercio);

        if ($this->filtroEstado !== '') {
            $query->where('bodega.fiados.estado', $this->filtroEstado);
        }

        if ($this->busqueda) {
            $query->whereHas('cliente', fn($q) =>
                $q->where('nombre', 'like', "%{$this->busqueda}%")
            );
        }

        $fiados   = $query->orderByDesc('fecha_creacion')->paginate(15);
        $clientes = Cliente::where('estado',1)
            ->where('id_comercio', Auth::user()->id_comercio)
            ->orderBy('nombre')->get();

        return view('livewire.fiados-component', compact('fiados','clientes'))
            ->layout('layouts.app');
    }
}