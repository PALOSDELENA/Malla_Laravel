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
}