<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargos extends Model
{
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    public function cargo(): HasMany 
    {
        return $this->hasMany(User::class, "usu_cargo");
    }
}
