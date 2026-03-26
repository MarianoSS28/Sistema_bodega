<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;

class HistorialVentasComponent extends Component
{
    public string $fechaDesde = '';
    public string $fechaHasta = '';
    public ?int   $ventaDetalle = null;

    public function mount(): void
    {
        $this->fechaDesde = now()->startOfMonth()->format('Y-m-d');
        $this->fechaHasta = now()->format('Y-m-d');
    }

    public function verDetalle(int $id): void
    {
        $this->ventaDetalle = ($this->ventaDetalle === $id) ? null : $id;
    }

    public function render()
    {
        $ventas = Venta::with('detalles.producto')
            ->where('estado', 1)
            ->when($this->fechaDesde, fn($q) => $q->whereDate('fecha_creacion', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('fecha_creacion', '<=', $this->fechaHasta))
            ->orderByDesc('fecha_creacion')
            ->get();

        $ventaAbierta = $this->ventaDetalle
            ? $ventas->firstWhere('id', $this->ventaDetalle)
            : null;

        return view('livewire.historial-ventas-component', compact('ventas', 'ventaAbierta'))
            ->layout('layouts.app');
    }
}