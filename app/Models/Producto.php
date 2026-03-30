<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'bodega.productos';

    protected $fillable = [
        'nombre',
        'codigo_barras',
        'precio',
        'stock',
        'estado',
        'estacion_creacion',
        'fecha_creacion',
        'estacion_modificacion',
        'fecha_modificacion',
        'id_comercio',
    ];

    public $timestamps = false;

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto');
    }
}
