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

Route::get('/login', LoginComponent::class)->name('login')->middleware('guest');
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Envolver las rutas existentes con auth:
Route::middleware(['auth', 'menu.acceso'])->group(function () {
    Route::get('/ventas'    ,   VentasComponent::class)->name('ventas');
    Route::get('/productos' ,   ProductosComponent::class)->name('productos');
    Route::get('/historial' ,   HistorialVentasComponent::class)->name('historial');
    Route::get('/vouchers'  ,   VouchersComponent::class)->name('vouchers');
    Route::get('/usuarios'  ,   UsuariosComponent::class)->name('usuarios');
    Route::get('/comercio'  ,   ComercioComponent::class)->name('comercio');
    Route::get('/menus'     ,   MenusComponent::class)->name('menus');
});

// Dashboard sin validación de menú (todos los autenticados lo ven)
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
});