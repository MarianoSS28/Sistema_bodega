<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class VouchersComponent extends Component
{
    use WithFileUploads;

    public string $idVenta  = '';
    public $foto            = null;
    public string $error    = '';
    public array  $vouchers = [];

    protected function rules(): array
    {
        return [
            'idVenta' => 'required|integer',
            'foto'    => 'required|image|max:4096',
        ];
    }

    public function mount(): void
    {
        $this->cargarVouchers();
    }

    public function cargarVouchers(): void
    {
        $this->vouchers = DB::select('EXEC bodega.sp_listar_vouchers @id_venta = NULL');
    }

    public function subirVoucher(): void
    {
        $this->validate();

        // Verificar que la venta existe
        $venta = DB::select('SELECT id FROM bodega.ventas WHERE id = ? AND estado = 1', [$this->idVenta]);
        if (empty($venta)) {
            $this->addError('idVenta', 'La venta no existe.');
            return;
        }

        $path = $this->foto->store('vouchers', 'public');

        DB::statement('EXEC bodega.sp_registrar_voucher @id_venta = ?, @foto_path = ?, @estacion = ?', [
            $this->idVenta,
            $path,
            request()->ip(),
        ]);

        $this->reset(['idVenta', 'foto']);
        $this->cargarVouchers();
        session()->flash('ok', 'Voucher subido correctamente.');
    }

    public function render()
    {
        return view('livewire.vouchers-component')
            ->layout('layouts.app');
    }
}