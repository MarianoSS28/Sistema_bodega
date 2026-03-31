<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RolesComponent extends Component
{
    public ?int   $editandoId = null;
    public string $nombre     = '';

    public bool $mostrarFormulario = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|min:2|max:100',
        ];
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->resetErrorBag();

        if ($id) {
            $r = Rol::findOrFail($id);
            $this->editandoId = $id;
            $this->nombre     = $r->nombre;
        } else {
            $this->reset(['editandoId', 'nombre']);
        }
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();

        DB::statement('EXEC bodega.sp_guardar_rol @id=?, @nombre=?, @actor=?', [
            $this->editandoId, $this->nombre, Auth::user()->nombre_completo,
        ]);

        $this->mostrarFormulario = false;
        $this->reset(['editandoId', 'nombre']);
        session()->flash('ok', 'Rol guardado.');
    }

    public function desactivar(int $id): void
    {
        DB::statement('EXEC bodega.sp_eliminar_rol @id=?, @actor=?', [
            $id, Auth::user()->nombre_completo,
        ]);
        session()->flash('ok', 'Rol eliminado.');
    }

    public function render()
    {
        $roles = Rol::where('estado', 1)->orderBy('id')->get();

        return view('livewire.roles-component', compact('roles'))
            ->layout('layouts.app');
    }
}