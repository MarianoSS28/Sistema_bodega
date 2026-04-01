<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FiadoPago extends Model
{
    protected $table = 'bodega.fiado_pagos';
    public $timestamps = false;
    protected $fillable = ['id_fiado','monto','metodo_pago','notas','estacion','usuario_creacion'];
}