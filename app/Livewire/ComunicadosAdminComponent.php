<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Comunicado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComunicadosAdminComponent extends Component
{
    public string $titulo    = '';
    public string $contenido = '';
    public bool   $mostrarFormulario = false;
    public ?int   $editandoId = null;

    protected function rules(): array {
        return [
            'titulo'    => 'required|min:3|max:255',
            'contenido' => 'required|min:10',
        ];
    }

    public function abrirFormulario(?int $id = null): void {
        $this->resetErrorBag();
        if ($id) {
            $c = Comunicado::findOrFail($id);
            $this->editandoId = $id;
            $this->titulo    = $c->titulo;
            $this->contenido = $c->contenido;
        } else {
            $this->reset(['editandoId','titulo','contenido']);
        }
        $this->mostrarFormulario = true;
    }

    public function guardar(): void {
        $this->validate();
        $actor = Auth::user()->nombre_completo;
        if ($this->editandoId) {
            Comunicado::where('id', $this->editandoId)->update([
                'titulo'               => $this->titulo,
                'contenido'            => $this->contenido,
                'usuario_modificacion' => $actor,
                'fecha_modificacion'   => now(),
            ]);
            // Borrar leídos para que vuelva a mostrarse
            DB::table('bodega.comunicado_leidos')->where('id_comunicado', $this->editandoId)->delete();
        } else {
            Comunicado::create([
                'titulo'           => $this->titulo,
                'contenido'        => $this->contenido,
                'estado'           => 1,
                'usuario_creacion' => $actor,
                'fecha_creacion'   => now(),
            ]);
        }
        $this->mostrarFormulario = false;
        $this->reset(['editandoId','titulo','contenido']);
        session()->flash('ok', 'Comunicado guardado.');
    }

    public function desactivar(int $id): void {
        Comunicado::where('id', $id)->update(['estado' => 0]);
        session()->flash('ok', 'Comunicado eliminado.');
    }

    public function render() {
        $comunicados = Comunicado::where('estado', 1)->orderByDesc('id')->get();
        return view('livewire.comunicados-admin-component', compact('comunicados'))
            ->layout('layouts.app');
    }
}