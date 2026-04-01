<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fiado extends Model
{
    protected $table = 'bodega.fiados';
    public $timestamps = false;
    protected $fillable = [
        'id_comercio','id_cliente','total','total_pagado','estado','estacion_creacion'
    ];

    public function cliente() {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    public function detalles() {
        return $this->hasMany(FiadoDetalle::class, 'id_fiado')->where('estado',1);
    }
    public function pagos() {
        return $this->hasMany(FiadoPago::class, 'id_fiado')->where('estado',1);
    }
}