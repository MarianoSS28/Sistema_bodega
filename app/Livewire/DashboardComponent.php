<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DashboardComponent extends Component
{
    public object|null $resumen    = null;
    public array       $ventasDias = [];

    public function mount(): void
    {
        $this->cargar();
    }

    public function cargar(): void
    {
        $result = DB::select('EXEC bodega.sp_dashboard_resumen');
        $this->resumen = $result[0] ?? null;

        $this->ventasDias = DB::select('EXEC bodega.sp_ventas_ultimos_dias @dias = 7');
    }

    public function render()
    {
        return view('livewire.dashboard-component')
            ->layout('layouts.app');
    }
}