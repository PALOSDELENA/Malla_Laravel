<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemExtras extends Model
{
    protected $table = 'item_extras';

    protected $fillable = [
        'nombre',
        'precio',
    ];

    public function cotizaciones()
    {
        return $this->belongsToMany(Cotizacion::class, 'cotizacion_item_extras', 'item_extra_id', 'cotizacion_id')
                    ->withPivot('nombre', 'cantidad', 'valor', 'suma_al_total')
                    ->withTimestamps();
    }
}
