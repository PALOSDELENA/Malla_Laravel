<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenConsumoMateriaPrima extends Model
{
    protected $table = "orden_consumo_m_prima";
    protected $guarded = [];
    public $timestamps = false;

    public function ordenProduccion()
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_produccion_id', 'id');
    }
}
