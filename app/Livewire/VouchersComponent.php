<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VouchersComponent extends Component
{
    use WithFileUploads;

    public string $idVenta  = '';
    public $foto            = null;
    public string $error    = '';
    public array  $vouchers = [];

    // Para edición
    public ?int   $editandoId    = null;
    public string $editIdVenta   = '';
    public $editFoto             = null;

    // Para ver foto ampliada
    public ?string $fotoAmpliada = null;

    protected function rules(): array
    {
        return [
            'idVenta' => 'required|integer',
            'foto'    => 'required|image|max:4096',
        ];
    }

    protected function editRules(): array
    {
        return [
            'editIdVenta' => 'required|integer',
            'editFoto'    => 'nullable|image|max:4096',
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

        // Validar 1 voucher por venta
        $existente = DB::select('SELECT id FROM bodega.voucher WHERE id_venta = ?', [$this->idVenta]);
        if (!empty($existente)) {
            $this->addError('idVenta', 'Esta venta ya tiene un voucher registrado. Use "Editar" para actualizarlo.');
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

    public function abrirEdicion(int $id): void
    {
        $voucher = collect($this->vouchers)->firstWhere('id', $id);
        if ($voucher) {
            $this->editandoId  = $id;
            $this->editIdVenta = (string) $voucher->id_venta;
            $this->editFoto    = null;
            $this->resetErrorBag();
        }
    }

    public function guardarEdicion(): void
    {
        $this->validate($this->editRules());

        // Verificar que la venta nueva no pertenezca a otro voucher (si cambia)
        $voucher = collect($this->vouchers)->firstWhere('id', $this->editandoId);
        if ((string)$voucher->id_venta !== $this->editIdVenta) {
            $venta = DB::select('SELECT id FROM bodega.ventas WHERE id = ? AND estado = 1', [$this->editIdVenta]);
            if (empty($venta)) {
                $this->addError('editIdVenta', 'La venta no existe.');
                return;
            }
            $existente = DB::select('SELECT id FROM bodega.voucher WHERE id_venta = ? AND id != ?', [$this->editIdVenta, $this->editandoId]);
            if (!empty($existente)) {
                $this->addError('editIdVenta', 'Esa venta ya tiene un voucher registrado.');
                return;
            }
        }

        $fotoPath = $voucher->foto_path;

        if ($this->editFoto) {
            // Borrar foto anterior
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $this->editFoto->store('vouchers', 'public');
        }

        DB::statement('UPDATE bodega.voucher SET id_venta = ?, foto_path = ?, estacion_modificacion = ?, fecha_modificacion = GETDATE() WHERE id = ?', [
            $this->editIdVenta,
            $fotoPath,
            request()->ip(),
            $this->editandoId,
        ]);

        $this->reset(['editandoId', 'editIdVenta', 'editFoto']);
        $this->cargarVouchers();
        session()->flash('ok', 'Voucher actualizado correctamente.');
    }

    public function cancelarEdicion(): void
    {
        $this->reset(['editandoId', 'editIdVenta', 'editFoto']);
        $this->resetErrorBag();
    }

    public function verFoto(string $path): void
    {
        $this->fotoAmpliada = $path;
    }

    public function cerrarFoto(): void
    {
        $this->fotoAmpliada = null;
    }

    public function render()
    {
        return view('livewire.vouchers-component')
            ->layout('layouts.app');
    }
}