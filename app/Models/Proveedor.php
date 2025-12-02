<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $guarded = [];

    public function productosNovedad()
    {
        return $this->belongsToMany(Productos::class, 'proveedores_producto', 'id_proveedor', 'id_producto')
            ->withPivot('calidad_producto', 'tiempo_entrega', 'presentacion_personal', 'observacion')
            ->orderBy('proveedores_producto.created_at', 'desc')
            ->withTimestamps();
    }

    public function productos()
    {
        return $this->hasMany(Productos::class, 'id_proveedor');
    }

    public function novedades()
    {
        return $this->hasMany(NovedadPaloteo::class, 'id_proveedor');
    }
}
