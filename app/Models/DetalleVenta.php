<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'bodega.detalle_ventas';

    protected $fillable = ['id_venta', 'id_producto', 'cantidad', 'precio_unitario', 'subtotal', 'estado', 'estacion_creacion', 'estacion_modificacion'];

    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
}
