<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FiadoDetalle extends Model
{
    protected $table = 'bodega.fiado_detalle';
    public $timestamps = false;
    protected $fillable = ['id_fiado','id_producto','cantidad','precio_unitario','subtotal','es_helada'];

    public function producto() {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}