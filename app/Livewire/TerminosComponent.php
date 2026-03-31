<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TerminosCondiciones;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TerminosComponent extends Component
{
    public ?int   $editandoId = null;
    public string $titulo    = '';
    public string $contenido = '';
    public string $version   = '1.0';

    public ?int   $viendoId  = null;   // para modal de vista previa

    public bool $mostrarFormulario = false;
    public bool $mostrarVista      = false;

    protected function rules(): array
    {
        return [
            'titulo'    => 'required|min:3|max:255',
            'version'   => 'required|max:20',
            'contenido' => 'required|min:10',
        ];
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->resetErrorBag();

        if ($id) {
            $t = TerminosCondiciones::findOrFail($id);
            $this->editandoId = $id;
            $this->titulo     = $t->titulo;
            $this->version    = $t->version;
            $this->contenido  = $t->contenido;
        } else {
            $this->reset(['editandoId', 'titulo', 'contenido']);
            // Sugerir versión siguiente
            $ultimo = TerminosCondiciones::where('estado', 1)->orderByDesc('id')->first();
            $this->version = $ultimo
                ? number_format((float) $ultimo->version + 0.1, 1)
                : '1.0';
        }
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();

        DB::statement('EXEC bodega.sp_guardar_terminos @id=?, @titulo=?, @contenido=?, @version=?, @actor=?', [
            $this->editandoId, $this->titulo, $this->contenido, $this->version,
            Auth::user()->nombre_completo,
        ]);
        
        DB::statement("
            UPDATE bodega.parametros
            SET valor = ?, usuario_modificacion = ?, fecha_modificacion = GETDATE()
            WHERE nombre = 'version_terminos_condiciones'
        ", [$this->version, Auth::user()->nombre_completo]);

        $this->mostrarFormulario = false;
        $this->reset(['editandoId', 'titulo', 'contenido', 'version']);
        session()->flash('ok', 'Términos guardados.');
    }

    public function desactivar(int $id): void
    {
        DB::statement('EXEC bodega.sp_eliminar_terminos @id=?, @actor=?', [
            $id, Auth::user()->nombre_completo,
        ]);
        session()->flash('ok', 'Registro eliminado.');
    }

    public function verDetalle(int $id): void
    {
        $this->viendoId      = $id;
        $this->mostrarVista  = true;
    }

    public function cerrarVista(): void
    {
        $this->mostrarVista = false;
        $this->viendoId     = null;
    }

    public function render()
    {
        $lista = TerminosCondiciones::where('estado', 1)
            ->orderByDesc('id')
            ->get();

        $terminoActivo = $lista->first();   // el más reciente es el vigente

        $vistaDetalle = $this->viendoId
            ? TerminosCondiciones::find($this->viendoId)
            : null;

        return view('livewire.terminos-component', compact('lista', 'terminoActivo', 'vistaDetalle'))
            ->layout('layouts.app');
    }
}