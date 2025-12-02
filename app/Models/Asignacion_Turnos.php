<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion_Turnos extends Model
{
    protected $table = 'asignacion_turnos';

    // Permitir la asignación masiva de todos los campos
    protected $guarded = [];

    public $timestamps = false;

    public function turno()
    {
        return $this->belongsTo(Turnos::class, 'turnos_id', 'id_turnos');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuarios_num_doc', 'num_doc');
    }

    // public function getTituloCardAttribute(): string
    // {
    //     $usuario = $this->usuario?->usu_nombre ?? 'Empleado';
    //     $punto = $this->usuario?->punto?->nombre ?? 'Punto';
    //     $turno = $this->turno?->tur_nombre ?? 'Turno';
    //     return "$usuario - $turno - $punto";
    // }

    // protected function getCardAttributes(): array
    // {
    //     return [
    //         'Detalle' => function ($record) {
    //             $usuario = $record->usuario?->usu_nombre ?? 'Empleado';
    //             $turno = $record->turno?->tur_nombre ?? 'Turno';
    //             return "{$usuario}<br><small>{$turno}</small>";
    //         },
    //     ];
    // }
}
