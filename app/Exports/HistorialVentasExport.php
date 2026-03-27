<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HistorialVentasExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected array $rows      = [];
    protected array $ventaRows = []; // índices de filas cabecera de venta
    protected array $totalRows = []; // índices de filas subtotal de venta
    protected int   $totalRow  = 0;  // índice de fila gran total

    public function __construct(protected Collection $ventas)
    {
        $this->buildRows();
    }

    private function buildRows(): void
    {
        // Encabezado general
        $this->rows[] = ['# Venta', 'Fecha', 'Producto', 'Cantidad', 'Precio Unit. (S/)', 'Subtotal (S/)'];
        $cursor = 2; // fila 1 = header

        foreach ($this->ventas as $venta) {
            // Fila cabecera de la venta
            $this->ventaRows[] = $cursor;
            $this->rows[] = [
                '#' . $venta->id,
                \Carbon\Carbon::parse($venta->fecha_creacion)->format('d/m/Y H:i'),
                '', '', '', '',
            ];
            $cursor++;

            // Filas de productos
            foreach ($venta->detalles as $detalle) {
                $this->rows[] = [
                    '',
                    '',
                    $detalle->producto->nombre ?? '—',
                    $detalle->cantidad,
                    number_format($detalle->precio_unitario, 2),
                    number_format($detalle->subtotal, 2),
                ];
                $cursor++;
            }

            // Fila subtotal de la venta
            $this->totalRows[] = $cursor;
            $this->rows[] = [
                '', '', 'Subtotal venta',
                $venta->detalles->sum('cantidad'),
                '',
                number_format($venta->total, 2),
            ];
            $cursor++;

            // Fila vacía separadora
            $this->rows[] = ['', '', '', '', '', ''];
            $cursor++;
        }

        // Fila gran total
        $this->totalRow = $cursor;
        $this->rows[] = [
            'TOTAL PERÍODO', '', '',
            $this->ventas->sum(fn($v) => $v->detalles->sum('cantidad')),
            '',
            number_format($this->ventas->sum('total'), 2),
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 18,
            'C' => 38,
            'D' => 12,
            'E' => 18,
            'F' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Solo el header global — sin rangos grandes
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF27B86D']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Filas cabecera de cada venta — verde oscuro
                foreach ($this->ventaRows as $row) {
                    $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF0F2D1E'], 'size' => 10],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD4F5E5']],
                    ]);
                }

                // Filas subtotal de cada venta — verde claro
                foreach ($this->totalRows as $row) {
                    $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8FAF2']],
                    ]);
                }

                // Fila gran total — verde marca
                $sheet->getStyle("A{$this->totalRow}:F{$this->totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF27B86D']],
                ]);

                // Alineación derecha en columnas numéricas solo hasta la última fila real
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("D2:F{$lastRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}