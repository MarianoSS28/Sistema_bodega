<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\VentasComponent;
use App\Livewire\ProductosComponent;
use App\Livewire\HistorialVentasComponent;
use App\Livewire\VouchersComponent;
use App\Livewire\DashboardComponent;

Route::get('/', fn() => redirect()->route('dashboard'));
Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
Route::get('/ventas',    VentasComponent::class)->name('ventas');
Route::get('/productos', ProductosComponent::class)->name('productos');
Route::get('/historial', HistorialVentasComponent::class)->name('historial');
Route::get('/vouchers',  VouchersComponent::class)->name('vouchers');