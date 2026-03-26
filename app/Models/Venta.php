<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'bodega.ventas';

    protected $fillable = ['total', 'estado', 'estacion_creacion', 'estacion_modificacion'];

    public $timestamps = false;

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }
}
