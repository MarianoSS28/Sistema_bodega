<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClientesComponent extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public ?int   $editandoId = null;
    public string $nombre    = '';
    public string $telefono  = '';
    public string $notas     = '';
    public bool   $mostrarFormulario = false;

    protected function rules(): array {
        return [
            'nombre'   => 'required|min:2|max:255',
            'telefono' => 'nullable|max:20',
            'notas'    => 'nullable|max:500',
        ];
    }

    public function updatedBusqueda(): void { $this->resetPage(); }

    public function abrirFormulario(?int $id = null): void {
        $this->resetErrorBag();
        if ($id) {
            $c = Cliente::findOrFail($id);
            $this->editandoId = $id;
            $this->nombre   = $c->nombre;
            $this->telefono = $c->telefono ?? '';
            $this->notas    = $c->notas ?? '';
        } else {
            $this->reset(['editandoId','nombre','telefono','notas']);
        }
        $this->mostrarFormulario = true;
    }

    public function guardar(): void {
        $this->validate();
        DB::statement('EXEC bodega.sp_guardar_cliente @id=?, @id_comercio=?, @nombre=?, @telefono=?, @notas=?, @actor=?', [
            $this->editandoId,
            Auth::user()->id_comercio,
            $this->nombre,
            $this->telefono ?: null,
            $this->notas    ?: null,
            Auth::user()->nombre_completo,
        ]);
        $this->mostrarFormulario = false;
        $this->reset(['editandoId','nombre','telefono','notas']);
        session()->flash('ok', 'Cliente guardado.');
    }

    public function desactivar(int $id): void {
        Cliente::where('id', $id)->update(['estado' => 0]);
        session()->flash('ok', 'Cliente eliminado.');
    }

    public function render() {
        $clientes = Cliente::where('estado', 1)
            ->where('id_comercio', Auth::user()->id_comercio)
            ->where('nombre', 'like', "%{$this->busqueda}%")
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.clientes-component', compact('clientes'))
            ->layout('layouts.app');
    }
}