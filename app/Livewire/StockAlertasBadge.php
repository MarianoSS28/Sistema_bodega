<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class StockAlertasBadge extends Component
{
    public int $totalAlertas = 0;
    public int $agotados     = 0;
    public int $porAcabar    = 0;
    public bool $mostrar     = false;
    public array $items      = [];

    public function mount(): void
    {
        $this->cargar();
    }

    public function cargar(): void
    {
        $result = DB::select('EXEC bodega.sp_alertas_stock @umbral = 5');
        $this->items      = collect($result)->toArray();
        $this->agotados   = collect($result)->where('tipo_alerta', 'AGOTADO')->count();
        $this->porAcabar  = collect($result)->where('tipo_alerta', 'POR_ACABAR')->count();
        $this->totalAlertas = count($result);
    }

    public function toggleMostrar(): void
    {
        $this->mostrar = !$this->mostrar;
    }

    public function render()
    {
        return view('livewire.stock-alertas-badge');
    }
}