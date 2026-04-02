<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\VentasComponent;
use App\Livewire\ProductosComponent;
use App\Livewire\HistorialVentasComponent;
use App\Livewire\VouchersComponent;
use App\Livewire\DashboardComponent;
use App\Livewire\LoginComponent;
use App\Livewire\UsuariosComponent;
use App\Livewire\ComercioComponent;
use App\Livewire\MenusComponent;
use App\Livewire\ComerciosAdminComponent;
use App\Livewire\RolesComponent;
use App\Livewire\TerminosComponent;
use App\Livewire\TicketsComponent;
use App\Livewire\MantenimientoAdminComponent;
use App\Livewire\AceptarTerminosComponent;
use App\Livewire\CambiarPasswordComponent;
use App\Livewire\ClientesComponent;
use App\Livewire\FiadosComponent;
use App\Livewire\ComunicadosAdminComponent;

Route::get('/mantenimiento', fn() => view('mantenimiento'))->name('mantenimiento');
Route::get('/login', LoginComponent::class)->name('login')->middleware('guest');
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::get('/terminos-condiciones', function () {
    $termino = \App\Models\TerminosCondiciones::where('estado', 1)
        ->orderByDesc('id')->first();
    return view('terminos-publico', compact('termino'));
})->name('terminos.publico');


Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
    Route::get('/aceptar-terminos', AceptarTerminosComponent::class)->name('aceptar-terminos');
    Route::get('/cambiar-password', CambiarPasswordComponent::class)->name('cambiar-password');  // <-- nueva
});

Route::middleware(['auth', 'menu.acceso'])->group(function () {
    Route::get('/ventas'                ,   VentasComponent::class)->name('ventas');
    Route::get('/productos'             ,   ProductosComponent::class)->name('productos');
    Route::get('/historial'             ,   HistorialVentasComponent::class)->name('historial');
    Route::get('/vouchers'              ,   VouchersComponent::class)->name('vouchers');
    Route::get('/usuarios'              ,   UsuariosComponent::class)->name('usuarios');
    Route::get('/comercio'              ,   ComercioComponent::class)->name('comercio');
    Route::get('/menus'                 ,   MenusComponent::class)->name('menus');
    Route::get('/comercios-admin'       ,   ComerciosAdminComponent::class)->name('comercios-admin');
    Route::get('/roles'                 ,   RolesComponent::class)->name('roles');
    Route::get('/terminos'              ,   TerminosComponent::class)->name('terminos');
    Route::get('/tickets'               ,   TicketsComponent::class)->name('tickets');
    Route::get('/mantenimiento-admin'   ,   MantenimientoAdminComponent::class)->name('mantenimiento-admin');
    Route::get('/clientes', ClientesComponent::class)->name('clientes');
    Route::get('/fiados',   FiadosComponent::class)->name('fiados');
    Route::get('/comunicados', ComunicadosAdminComponent::class)->name('comunicados');
});