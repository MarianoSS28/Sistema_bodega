<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MenusComponent extends Component
{
    public ?int   $editandoId = null;
    public string $nombre     = '';
    public string $ruta       = '';
    public string $icono      = '';
    public bool   $mostrarFormulario = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|min:2',
            'ruta'   => 'required',
            'icono'  => 'nullable|max:50',
        ];
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->resetErrorBag();
        if ($id) {
            $m = Menu::findOrFail($id);
            $this->editandoId = $id;
            $this->nombre = $m->nombre;
            $this->ruta   = $m->ruta;
            $this->icono  = $m->icono ?? '';
        } else {
            $this->reset(['editandoId','nombre','ruta','icono']);
        }
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();
        DB::statement('EXEC bodega.sp_guardar_menu ?, ?, ?, ?, ?', [
            $this->editandoId, $this->nombre, $this->ruta,
            $this->icono, Auth::user()->nombre_completo,
        ]);
        $this->mostrarFormulario = false;
        $this->reset(['editandoId','nombre','ruta','icono']);
        session()->flash('ok', 'Menú guardado.');
    }

    public function desactivar(int $id): void
    {
        DB::statement('EXEC bodega.sp_eliminar_menu ?', [$id]);
        session()->flash('ok', 'Menú eliminado.');
    }

    public function render()
    {
        $menus = Menu::where('estado', 1)->orderBy('id')->get();
        return view('livewire.menus-component', compact('menus'))->layout('layouts.app');
    }
}