<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMensaje extends Model
{
    protected $table      = 'bodega.ticket_mensajes';
    public    $timestamps = false;

    protected $fillable = [
        'id_ticket', 'id_usuario', 'mensaje', 'estado',
        'usuario_creacion', 'fecha_creacion',
    ];

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}