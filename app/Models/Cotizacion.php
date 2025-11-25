<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';
    
    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function items()
    {
        return $this->hasMany(CotizacionItem::class, 'cotizacion_id');
    }

    public function punto()
    {
        return $this->belongsTo(Puntos::class, 'sede');
    }

    public function itemExtras()
    {
        return $this->belongsToMany(ItemExtras::class, 'cotizacion_item_extras', 'cotizacion_id', 'item_extra_id')
                    ->withPivot('nombre', 'valor', 'suma_al_total')
                    ->withTimestamps();
    }
}
