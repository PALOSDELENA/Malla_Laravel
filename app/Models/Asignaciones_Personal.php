<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignaciones_Personal extends Model
{
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relaciones
    public function usuarios(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class,"usu_num_doc", "num_doc");
    }

    public function puntos(): BelongsTo
    {
        return $this->belongsTo(Puntos::class,"punto_origen_id");
    }
}
