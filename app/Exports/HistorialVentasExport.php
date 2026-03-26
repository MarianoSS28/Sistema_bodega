<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HistorialVentasExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected Collection $ventas) {}

    public function collection(): Collection
    {
        return $this->ventas;
    }

    public function headings(): array
    {
        return ['# Venta', 'Fecha', 'Producto', 'Cantidad', 'Precio Unit.', 'Subtotal', 'Total Venta'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            \Carbon\Carbon::parse($row->fecha_creacion)->format('d/m/Y H:i'),
            $row->detalles->map(fn($d) => $d->producto->nombre ?? '—')->implode(', '),
            $row->detalles->sum('cantidad'),
            '',
            $row->detalles->sum('subtotal'),
            $row->total,
        ];
    }
}