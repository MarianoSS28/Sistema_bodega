<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MantenimientoAdminComponent extends Component
{
    public bool   $activo  = false;
    public string $mensaje = '';

    public function mount(): void
    {
        $this->cargar();
    }

    private function cargar(): void
    {
        $modo = DB::select('EXEC bodega.sp_get_parametro @nombre=?', ['MODO_MANTENIMIENTO']);
        $this->activo = !empty($modo) && $modo[0]->valor === '1';

        $msg = DB::select('EXEC bodega.sp_get_parametro @nombre=?', ['MENSAJE_MANTENIMIENTO']);
        $this->mensaje = !empty($msg) ? $msg[0]->valor : 'Sistema en mantenimiento. Vuelve pronto.';
    }

    public function toggleMantenimiento(): void
    {
        $this->activo = !$this->activo;
        $this->guardar();
    }

    public function guardar(): void
    {
        $actor = Auth::user()->nombre_completo;

        DB::statement('EXEC bodega.sp_set_parametro @nombre=?, @valor=?, @actor=?', [
            'MODO_MANTENIMIENTO',
            $this->activo ? '1' : '0',
            $actor,
        ]);

        // Asegurarse de que el parámetro de mensaje exista
        $existe = DB::select('EXEC bodega.sp_get_parametro @nombre=?', ['MENSAJE_MANTENIMIENTO']);
        if (empty($existe)) {
            DB::statement(
                "INSERT INTO bodega.parametros(nombre, valor, estado, usuario_creacion, fecha_creacion)
                 VALUES('MENSAJE_MANTENIMIENTO', ?, 1, ?, GETDATE())",
                [$this->mensaje, $actor]
            );
        } else {
            DB::statement('EXEC bodega.sp_set_parametro @nombre=?, @valor=?, @actor=?', [
                'MENSAJE_MANTENIMIENTO', $this->mensaje, $actor,
            ]);
        }

        session()->flash('ok', $this->activo
            ? 'Modo mantenimiento ACTIVADO. Los usuarios no pueden acceder.'
            : 'Modo mantenimiento DESACTIVADO. Sistema disponible.'
        );
    }

    public function render()
    {
        return view('livewire.mantenimiento-admin-component')
            ->layout('layouts.app');
    }
}