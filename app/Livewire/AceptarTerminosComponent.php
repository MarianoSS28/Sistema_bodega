<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TerminosCondiciones;

class AceptarTerminosComponent extends Component
{
    public ?object $termino = null;

    public function mount(): void
    {
        // Si no está autenticado, redirigir al login directamente
        if (!Auth::check()) {
            redirect()->route('login')->send();
            return;
        }

        $this->termino = TerminosCondiciones::where('estado', 1)
            ->orderByDesc('id')->first();
    }

    public function aceptar(): void
    {
        $userId = Auth::id();

        DB::table('bodega.usuarios')
            ->where('id', $userId)
            ->update([
                'acepto_terminos'       => 1,
                'fecha_acepto_terminos' => now(),
            ]);

        // En vez de refresh(), re-autenticar directamente
        Auth::setUser(Auth::user()->fresh());

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function rechazar(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('login'), navigate: false);
    }

    public function render()
    {
        return view('livewire.aceptar-terminos-component')
            ->layout('layouts.guest');
    }
}