<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuarios extends Authenticatable
{
    // Definir que la llave primaria no es numérica
    public $incrementing = false;
    protected $keyType = "string";
    protected $primaryKey = "num_doc";

    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    // Relaciones
    public function seguridad(): HasOne
    {
        return $this->hasOne(Seguridad::class, "seg_usuario_id", "num_doc");
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargos::class,"usu_cargo");
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(Tipo_Documento::class,"t_doc");
    }

    public function estadosEspeciales(): HasMany
    {
        return $this->hasMany(Estados_Especiales::class,"des_usuario_id", "num_doc");
    }

    public function asignacionPersonal(): HasMany
    {
        return $this->hasMany(Asignaciones_Personal::class,"usu_num_doc", "num_doc");
    }

    public function turnos(): BelongsToMany
    {
        return $this->belongsToMany(Turnos::class,"asignacion_turnos","turnos_id", "usuarios_num_doc");
    }
    
    public function punto(): BelongsTo
    {
        return $this->belongsTo(Puntos::class, "usu_punto");
    }

    public function asignacionTurnos(): HasMany
    {
        return $this->hasMany(Asignacion_Turnos::class, "usuarios_num_doc", "num_doc");
    }
}