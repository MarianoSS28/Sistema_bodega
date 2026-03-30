<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'bodega.menus';
    public $timestamps = false;
    protected $fillable = ['nombre', 'ruta', 'icono', 'estado'];
}