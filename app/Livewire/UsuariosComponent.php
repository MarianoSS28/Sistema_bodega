<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Menu;
use App\Models\MenuUsuario;
use App\Models\Comercio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UsuariosComponent extends Component
{
    use WithPagination;

    public string $busqueda = '';

    // Form
    public ?int   $editandoId        = null;
    public string $nombre_completo   = '';
    public string $dni               = '';
    public string $password          = '';
    public string $id_rol            = '';
    public int    $id_comercio       = 0;
    public array  $menusSeleccionados = [];
    public bool   $mostrarFormulario  = false;

    // Bloqueo
    public int    $bloqueado       = 0;
    public string $motivo_bloqueo  = '';

    public function esAdmin(): bool
    {
        return (int) Auth::user()->id_rol === 1;
    }

    public function mount(): void
    {
        $this->id_comercio = Auth::user()->id_comercio;
    }

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->editandoId = $id;

        if ($id) {
            $u = Usuario::findOrFail($id);
            $this->nombre_completo  = $u->nombre_completo;
            $this->dni              = $u->dni;
            $this->password         = '';
            $this->id_rol           = (string) $u->id_rol;
            $this->id_comercio      = $u->id_comercio;
            $this->bloqueado        = (int)($u->bloqueado ?? 0);
            $this->motivo_bloqueo   = $u->motivo_bloqueo ?? '';
            $this->menusSeleccionados = MenuUsuario::where('id_usuario', $id)
                ->where('estado', 1)
                ->pluck('id_menu')
                ->map(fn($v) => (string) $v)
                ->toArray();
        } else {
            $this->reset(['nombre_completo', 'dni', 'password', 'id_rol', 'menusSeleccionados', 'bloqueado', 'motivo_bloqueo']);
            $this->id_comercio = Auth::user()->id_comercio;
        }
        $this->mostrarFormulario = true;
    }

    protected function rules(): array
    {
        return [
            'nombre_completo' => 'required|min:3',
            'dni'             => 'required|min:8',
            'id_rol'          => 'required',
            'id_comercio'     => 'required|integer|min:1',
            'password'        => $this->editandoId ? 'nullable|min:6' : 'required|min:6',
            'bloqueado'       => 'integer|in:0,1',
            'motivo_bloqueo'  => 'nullable|max:500',
        ];
    }

    public function guardar(): void
    {
        $this->validate();
        $actor = Auth::user()->nombre_completo;

        if ($this->editandoId) {
            $data = [
                'nombre_completo'      => $this->nombre_completo,
                'dni'                  => $this->dni,
                'id_rol'               => $this->id_rol,
                'id_comercio'          => $this->id_comercio,
                'bloqueado'            => $this->bloqueado,
                'motivo_bloqueo'       => $this->bloqueado ? $this->motivo_bloqueo : null,
                'usuario_modificacion' => $actor,
                'fecha_modificacion'   => now(),
            ];
            if ($this->password) {
                $data['password'] = bcrypt($this->password);
            }
            Usuario::where('id', $this->editandoId)->update($data);

            MenuUsuario::where('id_usuario', $this->editandoId)->update(['estado' => 0]);
            foreach ($this->menusSeleccionados as $idMenu) {
                MenuUsuario::updateOrCreate(
                    ['id_usuario' => $this->editandoId, 'id_menu' => $idMenu],
                    [
                        'estado'               => 1,
                        'usuario_creacion'     => $actor,
                        'fecha_creacion'       => now(),
                        'usuario_modificacion' => $actor,
                        'fecha_modificacion'   => now(),
                    ]
                );
            }
            session()->flash('ok', 'Usuario actualizado.');
        } else {
            $id = DB::table('bodega.usuarios')->insertGetId([
                'nombre_completo'  => $this->nombre_completo,
                'dni'              => $this->dni,
                'email'            => $this->dni . '@bodega.local',
                'password'         => bcrypt($this->password),
                'id_rol'           => $this->id_rol,
                'id_comercio'      => $this->id_comercio,
                'estado'           => 1,
                'bloqueado'        => 0,
                'acepto_terminos'  => 0,
                'usuario_creacion' => $actor,
                'fecha_creacion'   => now(),
            ]);
            foreach ($this->menusSeleccionados as $idMenu) {
                DB::table('bodega.menu_usuario')->insert([
                    'id_usuario'       => $id,
                    'id_menu'          => $idMenu,
                    'estado'           => 1,
                    'usuario_creacion' => $actor,
                    'fecha_creacion'   => now(),
                ]);
            }
            session()->flash('ok', 'Usuario creado.');
        }

        $this->mostrarFormulario = false;
        $this->reset(['nombre_completo', 'dni', 'password', 'id_rol', 'menusSeleccionados', 'bloqueado', 'motivo_bloqueo']);
    }

    public function desactivar(int $id): void
    {
        Usuario::where('id', $id)->update([
            'estado'               => 0,
            'usuario_modificacion' => Auth::user()->nombre_completo,
            'fecha_modificacion'   => now(),
        ]);
        session()->flash('ok', 'Usuario desactivado.');
    }

    public function toggleBloqueo(int $id): void
    {
        $u = Usuario::findOrFail($id);
        Usuario::where('id', $id)->update([
            'bloqueado'            => $u->bloqueado ? 0 : 1,
            'usuario_modificacion' => Auth::user()->nombre_completo,
            'fecha_modificacion'   => now(),
        ]);
        session()->flash('ok', $u->bloqueado ? 'Usuario desbloqueado.' : 'Usuario bloqueado.');
    }

    public function restablecerPassword(int $id): void
    {
        $u = Usuario::findOrFail($id);
 
        Usuario::where('id', $id)->update([
            'password'              => bcrypt($u->dni),
            'debe_cambiar_password' => 1,         
            'usuario_modificacion'  => Auth::user()->nombre_completo,
            'fecha_modificacion'    => now(),
        ]);
 
        session()->flash('ok', "Contraseña restablecida al DNI ({$u->dni}). El usuario deberá cambiarla al iniciar sesión.");
    }

    public function render()
    {
        $query = Usuario::with('rol')->where('estado', 1);

        if (!$this->esAdmin()) {
            $query->where('id_comercio', Auth::user()->id_comercio);
        }

        $usuarios = $query
            ->where(fn($q) =>
                $q->where('nombre_completo', 'like', "%{$this->busqueda}%")
                  ->orWhere('dni', 'like', "%{$this->busqueda}%")
            )
            ->orderByDesc('id')
            ->paginate(10);

        $roles     = Rol::where('estado', 1)->get();
        $menus     = Menu::where('estado', 1)->orderBy('id')->get();
        $comercios = Comercio::where('estado', 1)->orderBy('nombre')->get();

        return view('livewire.usuarios-component', compact('usuarios', 'roles', 'menus', 'comercios'))
            ->layout('layouts.app');
    }
}