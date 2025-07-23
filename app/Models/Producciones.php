<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producciones extends Model
{
    protected $table = "producciones";
    protected $guarded = [];

    public $timestamps = false;

    // public function productos(): BelongsToMany
    // {
    //     return $this->belongsToMany(Productos::class, 'produccion_m_prima', 'produccion_id', 'id')
    //                 ->withPivot('cantidad');
    // }

    public function materiasPrimas()
    {
        return $this->hasMany(ProduccioneProducto::class, 'produccion_id');
    }

    public function ordenProduccion()
    {
        return $this->hasMany(OrdenProduccion::class, 'produccion_id', 'id');
    }
}
