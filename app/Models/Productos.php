<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $table = "productos";

    protected $guarded = [];

    public $timestamps = false;


    public function productosTra()
    {
        return $this->hasMany(TrazabilidadProducto::class, 'traIdProducto', 'id');
    }

    // public function usosEnProduccion()
    // {
    //     return $this->hasMany(ProduccioneProducto::class, 'm_prima_id', 'id');
    // }

    public function producciones()
    {
        return $this->belongsToMany(Producciones::class, 'produccion_m_prima', 'm_prima_id', 'produccion_id')
            ->withPivot('cantidad_requerida');
    }

    public function stock()
    {
        return $this->hasOne(ProductoStock::class, 'producto_id', 'id');
    }

    public function secciones()
    {
        return $this->belongsTo(Seccion::class, 'proSeccion', 'id');
    }

    public function ordenCompra()
    {
        return $this->belongsToMany(OrdenCompra::class, 'orden_producto', 'producto_id', 'orden_id')
            ->withPivot('inventario', 'sugerido', 'pedido_1', 'pedido_2', 'precio_total', 'observaciones', 'cantidad_bodega', 'stock_minimo')
            ->withTimestamps();
    }

    public function novedadPaloteo()
    {
        return $this->hasMany(NovedadPaloteo::class, 'id_producto', 'id');
    }

    public function proveedorNovedad()
    {
        return $this->belongsToMany(Productos::class, 'proveedores_producto', 'id_producto', 'id_proveedor')
            ->withPivot('calidad_producto', 'tiempo_entrega', 'presentacion_personal', 'observacion');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id');
    }

    public function cotizacionItems()
    {
        return $this->hasMany(CotizacionItem::class, 'id_producto');
    }
}