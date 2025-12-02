<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionItem extends Model
{
    protected $table = 'cotizacion_items';
    
    protected $guarded = [];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }
}
