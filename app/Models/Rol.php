<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'bodega.roles';
    public $timestamps = false;
    protected $fillable = ['nombre', 'estado'];
}