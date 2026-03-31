<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminosCondiciones extends Model
{
    protected $table      = 'bodega.terminos_condiciones';
    public    $timestamps = false;

    protected $fillable = [
        'titulo', 'contenido', 'version', 'estado',
        'usuario_creacion', 'fecha_creacion',
        'usuario_modificacion', 'fecha_modificacion',
    ];
}