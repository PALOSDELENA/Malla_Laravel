<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producciones extends Model
{
    protected $table = "producciones";
    protected $guarded = [];

    public $timestamps = false;

    // public function materiasPrimas()
    // {
    //     return $this->hasMany(ProduccioneProducto::class, 'produccion_id', 'id');
    // }

    public function productos()
    {
        return $this->belongsToMany(Productos::class, 'produccion_m_prima', 'produccion_id', 'm_prima_id')
            ->withPivot('cantidad_requerida');
    }

    public function ordenProduccion()
    {
        return $this->hasMany(OrdenProduccion::class, 'produccion_id', 'id');
    }

    public function produccioneProductos(): BelongsToMany
    {
        return $this->belongsToMany(Productos::class, 'produccion_m_prima', 'produccion_id', 'm_prima_id')
            ->withPivot('cantidad_requerida');
    }
}
