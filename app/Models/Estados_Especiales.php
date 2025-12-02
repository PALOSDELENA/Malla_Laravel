<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estados_Especiales extends Model
{
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relaciones
    protected function usuarios(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, "des_usuario_id", "num_doc");
    }
}
