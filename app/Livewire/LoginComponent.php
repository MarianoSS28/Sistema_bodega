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
            $user = Auth::user();
            $esAdmin = (int)$user->id_rol === 1;

            // Verificar bloqueo de usuario
            if (!$esAdmin && (int)($user->bloqueado ?? 0) === 1) {
                Auth::logout();
                $this->error = $user->motivo_bloqueo ?? 'Tu cuenta ha sido bloqueada.';
                return;
            }

            // Verificar bloqueo de comercio
            if (!$esAdmin) {
                $comercio = \Illuminate\Support\Facades\DB::selectOne(
                    'SELECT bloqueado, motivo_bloqueo FROM bodega.comercio WHERE id = ? AND estado = 1',
                    [$user->id_comercio]
                );
                if ($comercio && (int)$comercio->bloqueado === 1) {
                    Auth::logout();
                    $this->error = $comercio->motivo_bloqueo ?? 'Tu comercio ha sido bloqueado.';
                    return;
                }

                // Verificar modo mantenimiento — admins siempre pasan
                if (!$esAdmin) {
                    try {
                        $r = \Illuminate\Support\Facades\DB::select(
                            'EXEC bodega.sp_get_parametro @nombre=?', ['MODO_MANTENIMIENTO']
                        );
                        if (!empty($r) && $r[0]->valor === '1') {
                            Auth::logout();
                            $this->error = 'El sistema está en mantenimiento. Intenta más tarde.';
                            return;
                        }
                    } catch (\Throwable) {}
                }
            }

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