<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario_Registros extends Model
{
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relaciones
    public function puntos():BelongsTo
    {
        return $this->belongsTo(Puntos::class, "punto_id");
    }

    public function inventarioProductos():BelongsTo
    {
        return $this->belongsTo(Inventario_Productos::class,"punto_id");
    }
}
