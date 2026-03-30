<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginComponent extends Component
{
    public string $dni      = '';
    public string $password = '';
    public string $error    = '';

    public function login(): void
    {
        if (Auth::attempt(['dni' => $this->dni, 'password' => $this->password, 'estado' => 1])) {
            session()->regenerate();
            $this->redirect(route('dashboard'), navigate: true);
            return;
        }
        $this->error = 'DNI o contraseña incorrectos.';
    }

    public function render()
    {
        return view('livewire.login-component')
            ->layout('layouts.guest');
    }
}