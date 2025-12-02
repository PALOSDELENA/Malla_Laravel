<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    protected $table = "orden_produccion";

    protected $primaryKey = 'id';
    protected $guarded = [];
    public $timestamps = false;

    public function responsable1()
    {
        return $this->belongsTo(User::class, 'responsable', 'num_doc');
    }

    public function producciones()
    {
        return $this->belongsTo(Producciones::class, 'produccion_id', 'id');
    }

    // Consumos reales registrados en orden_consumo_m_prima
    public function consumoMateriaPrima()
    {
        return $this->hasMany(TrazabilidadProducto::class, 'orden_produccion_id', 'id');
    }
}
