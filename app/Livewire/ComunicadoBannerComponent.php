<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Comunicado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComunicadoBannerComponent extends Component
{
    public ?object $comunicado = null;
    public bool    $mostrar    = false;

    public function mount(): void {
        $this->verificar();
    }

    public function verificar(): void {
        $userId = Auth::id();
        $this->comunicado = Comunicado::where('estado', 1)
            ->whereNotExists(function($q) use ($userId) {
                $q->select(DB::raw(1))
                  ->from('bodega.comunicado_leidos')
                  ->whereColumn('id_comunicado', 'bodega.comunicados.id')
                  ->where('id_usuario', $userId);
            })
            ->orderByDesc('id')
            ->first();
        $this->mostrar = $this->comunicado !== null;
    }

    public function aceptar(): void {
        if (!$this->comunicado) return;
        DB::table('bodega.comunicado_leidos')->insert([
            'id_comunicado' => $this->comunicado->id,
            'id_usuario'    => Auth::id(),
            'fecha_leido'   => now(),
        ]);
        $this->mostrar    = false;
        $this->comunicado = null;
    }

    public function render() {
        return view('livewire.comunicado-banner-component');
    }
}