<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo_Documento extends Model
{
    protected $table = "tipo_documento";
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relaciones
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuarios::class, "t_doc");
    }
}
