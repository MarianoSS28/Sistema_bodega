<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\HistorialVentasExport;
use App\Exports\HistorialVentasResumenExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HistorialVentasComponent extends Component
{
    public string $fechaDesde      = '';
    public string $fechaHasta      = '';
    public string $filtroProducto  = '';
    public ?int   $ventaDetalle    = null;

    public bool   $mostrarModalExport = false;
    public string $tipoExport         = '';

    public function mount(): void
    {
        $this->fechaDesde = now()->startOfMonth()->format('Y-m-d');
        $this->fechaHasta = now()->format('Y-m-d');
    }

    public function verDetalle(int $id): void
    {
        $this->ventaDetalle = ($this->ventaDetalle === $id) ? null : $id;
    }

    private function getVentas()
    {
        return Venta::with('detalles.producto')
            ->where('estado', 1)
            ->when($this->fechaDesde, fn($q) => $q->whereDate('fecha_creacion', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('fecha_creacion', '<=', $this->fechaHasta))
            ->when($this->filtroProducto, fn($q) => $q->whereHas('detalles.producto', fn($q2) =>
                $q2->where('nombre', 'like', "%{$this->filtroProducto}%")
            ))
            ->orderByDesc('fecha_creacion')
            ->get();
    }

    public function abrirModalExport(string $tipo): void
    {
        $this->tipoExport = $tipo;
        $this->mostrarModalExport = true;
    }

    /**
     * Livewire intercepta BinaryFileResponse y StreamedResponse
     * y los envía como descarga — el tipo de retorno declarado
     * resuelve el error "void function must not return a value".
     */
    public function exportar(string $modo): BinaryFileResponse|StreamedResponse
    {
        $ventas     = $this->getVentas();
        $fechaDesde = $this->fechaDesde;
        $fechaHasta = $this->fechaHasta;
        $this->mostrarModalExport = false;

        if ($this->tipoExport === 'excel') {
            $export   = $modo === 'detallado'
                ? new HistorialVentasExport($ventas)
                : new HistorialVentasResumenExport($ventas);
            $filename = $modo === 'detallado'
                ? 'historial_ventas_detallado.xlsx'
                : 'historial_ventas_resumen.xlsx';

            return Excel::download($export, $filename);
        }

        // PDF
        $vista    = $modo === 'detallado' ? 'exports.historial-pdf' : 'exports.historial-pdf-resumen';
        $filename = $modo === 'detallado' ? 'historial_ventas_detallado.pdf' : 'historial_ventas_resumen.pdf';

        $pdf = Pdf::loadView($vista, compact('ventas', 'fechaDesde', 'fechaHasta'));

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }

    public function render()
    {
        $ventas = $this->getVentas();

        $ventaAbierta = $this->ventaDetalle
            ? $ventas->firstWhere('id', $this->ventaDetalle)
            : null;

        return view('livewire.historial-ventas-component', compact('ventas', 'ventaAbierta'))
            ->layout('layouts.app');
    }
}