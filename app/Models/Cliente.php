<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'bodega.clientes';
    public $timestamps = false;
    protected $fillable = ['id_comercio','nro_cliente','nombre','telefono','notas','estado'];

    public function fiados() {
        return $this->hasMany(Fiado::class, 'id_cliente');
    }
}