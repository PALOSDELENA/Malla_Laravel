<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventario_Productos extends Model
{
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relaciones
    public function inventarioRegistros():HasMany
    {
        return $this->hasMany(Inventario_Registros::class, "producto_id");
    }

    public function inventarioSecciones():BelongsTo
    {
        return $this->belongsTo(Inventario_Secciones::class,"seccion_id");
    }
}
