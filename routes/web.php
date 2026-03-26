<?php

use App\Livewire\HistorialVentasComponent;
use App\Livewire\ProductosComponent;
use App\Livewire\VentasComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('ventas'));
Route::get('/ventas', VentasComponent::class)->name('ventas');
Route::get('/productos', ProductosComponent::class)->name('productos');
Route::get('/historial', HistorialVentasComponent::class)->name('historial');
