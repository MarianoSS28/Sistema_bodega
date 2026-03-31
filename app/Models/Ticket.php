<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table      = 'bodega.tickets';
    public    $timestamps = false;

    protected $fillable = [
        'id_comercio', 'id_usuario', 'asunto', 'mensaje',
        'estado', 'prioridad',
        'usuario_creacion', 'fecha_creacion',
        'usuario_modificacion', 'fecha_modificacion',
    ];

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function comercio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Comercio::class, 'id_comercio');
    }

    public function mensajes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketMensaje::class, 'id_ticket');
    }

    /** Número formateado: T-<idcomercio><00000000> */
    public function getNumeroAttribute(): string
    {
        return 'T-' . $this->id_comercio . str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    public function getEstadoLabelAttribute(): string
    {
        return match((int) $this->estado) {
            1 => 'Abierto',
            2 => 'En proceso',
            3 => 'Cerrado',
            default => 'Desconocido',
        };
    }

    public function getPrioridadLabelAttribute(): string
    {
        return match($this->prioridad) {
            'alta'    => 'Alta',
            'urgente' => 'Urgente',
            default   => 'Normal',
        };
    }
}