<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Filament\Models\Contracts\HasName;

class User extends Authenticatable implements HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    public $incrementing = false;
    protected $keyType = "string";
    protected $primaryKey = "num_doc";

    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    public function getFilamentName(): string
    {
        return $this->usu_nombre ?? 'Sin nombre';
    }

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

    // public function turnos(): BelongsToMany
    // {
    //     return $this->belongsToMany(Turnos::class,"asignacion_turnos","turnos_id", "usuarios_num_doc");
    // }
    
    public function punto(): BelongsTo
    {
        return $this->belongsTo(Puntos::class, "usu_punto");
    }

    public function asignacionTurnos(): HasMany
    {
        return $this->hasMany(Asignacion_Turnos::class, "usuarios_num_doc", "num_doc");
    }

    public function trazabilidadProductos()
    {
        return $this->hasMany(TrazabilidadProducto::class,'traResponsable', 'num_doc');
    }

    public function ordenProduccion(): HasMany
    {
        return $this->hasMany(OrdenProduccion::class, 'responsable', 'num_doc');
    }

    public function novedadPaloteo()
    {
        return $this->hasMany(NovedadPaloteo::class, 'id_usuario', 'num_doc');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
