<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HistorialVentasResumenExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected int $totalRow = 0;

    public function __construct(protected Collection $ventas) {}

    public function array(): array
    {
        $rows = [];

        // Header
        $rows[] = ['# Venta', 'Fecha', 'Total Productos', 'Total Venta (S/)'];

        // Una fila por venta
        foreach ($this->ventas as $venta) {
            $rows[] = [
                '#' . $venta->id,
                \Carbon\Carbon::parse($venta->fecha_creacion)->format('d/m/Y H:i'),
                $venta->detalles->sum('cantidad'),
                number_format($venta->total, 2),
            ];
        }

        // Fila total — guardamos el índice (1-based: header + n ventas + 1)
        $this->totalRow = count($rows) + 1;
        $rows[] = [
            'TOTAL PERÍODO',
            '',
            $this->ventas->sum(fn($v) => $v->detalles->sum('cantidad')),
            number_format($this->ventas->sum('total'), 2),
        ];

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 18,
            'C' => 16,
            'D' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header verde
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
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Filas alternas — solo hasta la última fila real
                for ($i = 2; $i < $lastRow; $i++) {
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$i}:D{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF4FDF8']],
                        ]);
                    }
                }

                // Fila gran total
                $sheet->getStyle("A{$lastRow}:D{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF27B86D']],
                ]);

                // Alineación derecha en columnas numéricas
                $sheet->getStyle("C2:D{$lastRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}