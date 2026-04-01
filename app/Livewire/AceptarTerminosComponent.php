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
        if (!Auth::check()) {
            redirect()->route('login')->send();
            return;
        }

        $this->termino = TerminosCondiciones::where('estado', 1)
            ->orderByDesc('id')->first();
    }

    public function aceptar(): void
    {
        $user   = Auth::user();
        $userId = $user->id;

        // ¿Es la primera vez que acepta los términos?
        $primeraVez = (int)($user->acepto_terminos ?? 0) === 0;

        DB::table('bodega.usuarios')
            ->where('id', $userId)
            ->update([
                'acepto_terminos'       => 1,
                'fecha_acepto_terminos' => now(),
                // Si es primera vez, forzamos cambio de contraseña
                'debe_cambiar_password' => $primeraVez ? 1 : DB::raw('debe_cambiar_password'),
            ]);

        Auth::setUser(Auth::user()->fresh());

        // Si es primera vez lo mandamos a cambiar contraseña, si no al dashboard
        if ($primeraVez) {
            $this->redirect(route('cambiar-password'), navigate: false);
        } else {
            $this->redirect(route('dashboard'), navigate: false);
        }
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