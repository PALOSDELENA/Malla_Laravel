<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventario_Secciones extends Model
{
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relaciones
    public function inventarioProductos(): HasMany
    {
        return $this->hasMany(Inventario_Productos::class, "seccion_id");
    }
}
