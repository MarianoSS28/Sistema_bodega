<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MenuUsuario extends Model
{
    protected $table = 'bodega.menu_usuario';
    public $timestamps = false;
    protected $fillable = ['id_menu', 'id_usuario', 'estado', 'usuario_creacion', 'fecha_creacion'];
}