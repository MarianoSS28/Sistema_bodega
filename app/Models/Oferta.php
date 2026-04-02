<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    protected $table = 'bodega.ofertas';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'descripcion', 'tipo', 'valor',
        'cantidad_paga', 'cantidad_lleva',
        'fecha_inicio', 'fecha_fin',
        'activa', 'id_comercio', 'estado',
        'usuario_creacion', 'fecha_creacion',
        'usuario_modificacion', 'fecha_modificacion',
    ];

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'bodega.oferta_productos',
            'id_oferta',
            'id_producto'
        )->wherePivot('estado', 1)->where('bodega.productos.estado', 1);
    }

    /** Etiqueta legible del tipo */
    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            '2x1'        => '2×1',
            'nxm'        => "{$this->cantidad_paga}×{$this->cantidad_lleva}",
            'porcentaje' => number_format($this->valor, 0) . '% desc.',
            'fijo'       => 'S/ ' . number_format($this->valor, 2) . ' desc.',
            default      => $this->tipo,
        };
    }

    /** Calcula el precio final de un ítem dado el qty en carrito */
    public function aplicar(float $precioUnitario, int $cantidad): float
    {
        return match ($this->tipo) {
            '2x1' => $this->precio2x1($precioUnitario, $cantidad),
            'nxm' => $this->precioNxM($precioUnitario, $cantidad),
            'porcentaje' => $precioUnitario * $cantidad * (1 - $this->valor / 100),
            'fijo'       => max(0, ($precioUnitario - $this->valor) * $cantidad),
            default      => $precioUnitario * $cantidad,
        };
    }

    private function precio2x1(float $precio, int $cantidad): float
    {
        $paga  = intdiv($cantidad, 2) + ($cantidad % 2);
        return $paga * $precio;
    }

    private function precioNxM(float $precio, int $cantidad): float
    {
        $paga   = (int) ($this->cantidad_paga   ?? 2);
        $lleva  = (int) ($this->cantidad_lleva  ?? 3);
        if ($lleva <= 0) return $precio * $cantidad;
        $grupos = intdiv($cantidad, $lleva);
        $resto  = $cantidad % $lleva;
        return ($grupos * $paga + $resto) * $precio;
    }
}