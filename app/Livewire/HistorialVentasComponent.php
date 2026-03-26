<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\HistorialVentasExport;

class HistorialVentasComponent extends Component
{
    public string $fechaDesde      = '';
    public string $fechaHasta      = '';
    public string $filtroProducto  = '';
    public ?int   $ventaDetalle    = null;

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

    public function exportarExcel()
    {
        $ventas = $this->getVentas();
        return Excel::download(new HistorialVentasExport($ventas), 'historial_ventas.xlsx');
    }

    public function exportarPdf()
    {
        $ventas    = $this->getVentas();
        $fechaDesde = $this->fechaDesde;
        $fechaHasta = $this->fechaHasta;
        $pdf = Pdf::loadView('exports.historial-pdf', compact('ventas', 'fechaDesde', 'fechaHasta'));
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'historial_ventas.pdf'
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