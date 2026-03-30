<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Comercio extends Model
{
    protected $table = 'bodega.comercio';
    public $timestamps = false;
    protected $fillable = ['nombre', 'direccion', 'logo_path', 'yape_qr', 'plin_qr', 'otro_pago', 'estado', 'usuario_creacion', 'fecha_creacion'];
}