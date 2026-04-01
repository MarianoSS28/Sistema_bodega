<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CambiarPasswordComponent extends Component
{
    public string $password_nuevo    = '';
    public string $password_confirm  = '';
    public string $error             = '';

    public function mount(): void
    {
        if (!Auth::check()) {
            redirect()->route('login')->send();
            return;
        }

        // Si no necesita cambiar contraseña, mandarlo al dashboard
        if ((int)(Auth::user()->debe_cambiar_password ?? 0) === 0) {
            redirect()->route('dashboard')->send();
        }
    }

    protected function rules(): array
    {
        return [
            'password_nuevo'   => 'required|min:6',
            'password_confirm' => 'required|same:password_nuevo',
        ];
    }

    protected $messages = [
        'password_nuevo.required'   => 'La nueva contraseña es obligatoria.',
        'password_nuevo.min'        => 'La contraseña debe tener al menos 6 caracteres.',
        'password_confirm.required' => 'Debes confirmar la contraseña.',
        'password_confirm.same'     => 'Las contraseñas no coinciden.',
    ];

    public function guardar(): void
    {
        $this->error = '';
        $this->validate();

        $userId = Auth::id();

        DB::table('bodega.usuarios')
            ->where('id', $userId)
            ->update([
                'password'              => bcrypt($this->password_nuevo),
                'debe_cambiar_password' => 0,
                'usuario_modificacion'  => Auth::user()->nombre_completo,
                'fecha_modificacion'    => now(),
            ]);

        // Refrescar el usuario en sesión
        Auth::setUser(Auth::user()->fresh());

        session()->flash('ok', 'Contraseña actualizada correctamente.');

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.cambiar-password-component')
            ->layout('layouts.guest');
    }
}