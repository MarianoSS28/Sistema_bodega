<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'bodega.usuarios';

    public $timestamps = false;

    protected $fillable = [
        'nombre_completo', 'dni', 'password', 'email', 'id_rol', 'estado',
        'acepto_terminos', 'fecha_acepto_terminos', 'bloqueado', 'motivo_bloqueo',   
        'id_comercio',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    // Laravel Auth necesita getAuthIdentifierName y getAuthPassword
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function comercio()
    {
        return $this->belongsTo(Comercio::class, 'id_comercio');
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function menus()
    {
        return $this->hasManyThrough(
            Menu::class, MenuUsuario::class,
            'id_usuario', 'id', 'id', 'id_menu'
        )->where('bodega.menu_usuario.estado', 1)
            ->where('bodega.menus.estado', 1);
    }

    // Helper rápido para checkear acceso
    public function tieneAcceso(string $ruta): bool
    {
        return $this->menus()->where('ruta', $ruta)->exists();
    }
}
