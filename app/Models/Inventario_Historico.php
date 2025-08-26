<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario_Historico extends Model
{
    protected $table = 'inventario_historico';
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relaciones
    public function puntos(): BelongsTo
    {
        return $this->belongsTo(Puntos::class, "punto_id");
    }
}
