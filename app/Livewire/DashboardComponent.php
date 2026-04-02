<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DashboardComponent extends Component
{
    public object|null $resumen    = null;
    public array       $ventasDias = [];
    public array $pagosFiadoDias = [];

    public function mount(): void
    {
        $this->cargar();
    }

    public function cargar(): void
    {
        $idComercio = Auth::user()->id_comercio;
        $result = DB::select('EXEC bodega.sp_dashboard_resumen @id_comercio = ?', [$idComercio]);
        $this->resumen = $result[0] ?? null;
        $this->ventasDias = DB::select('EXEC bodega.sp_ventas_ultimos_dias @dias = 7, @id_comercio = ?', [$idComercio]);
        $this->pagosFiadoDias = DB::select('EXEC bodega.sp_pagos_fiado_ultimos_dias @dias = 7, @id_comercio = ?', [$idComercio]);
    }

    public function render()
    {
        return view('livewire.dashboard-component')
            ->layout('layouts.app');
    }
}