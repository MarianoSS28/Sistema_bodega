<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Comunicado extends Model
{
    protected $table = 'bodega.comunicados';
    public $timestamps = false;
    protected $fillable = ['titulo', 'contenido', 'estado', 'usuario_creacion', 'fecha_creacion'];
}