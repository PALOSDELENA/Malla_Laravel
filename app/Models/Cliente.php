<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'celular',
        'correo'
    ];

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_cliente');
    }
}
