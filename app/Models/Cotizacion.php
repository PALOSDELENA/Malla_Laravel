<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function items()
    {
        return $this->hasMany(CotizacionItem::class, 'id_cotizacion');
    }
}
