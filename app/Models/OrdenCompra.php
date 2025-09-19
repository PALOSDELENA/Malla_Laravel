<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    protected $table = "orden_compra";

    protected $guarded = [];

    public function punto()
    {
        return $this->belongsTo(Puntos::class, "punto_id");
    }

    public function producto()
    {
        return $this->belongsToMany(Productos::class, 'orden_producto', 'orden_id', 'producto_id')
            ->withPivot('inventario', 'sugerido', 'pedido_1', 'pedido_2', 'total_pedido', 'precio_total', 'observaciones', 'cantidad_bodega', 'stock_minimo')
            ->withTimestamps();
    }
}
