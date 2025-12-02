<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Turnos extends Model
{
    protected $primaryKey = 'id_turnos';
    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relacion
    // public function usuarios(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class,"asignacion_turnos","usuarios_num_doc","turnos_id")
    //         ->as("asignacionTurnos")
    //         ->withPivot("tur_usu_dia", "tur_usu_fecha");
    // }

    public function asignacionTurnos()
    {
        return $this->hasMany(Asignacion_Turnos::class, 'turnos_id', 'id_turnos');
    }
}
