<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NovedadPaloteo extends Model
{
    protected $table = 'novedades_paloteo';
    
    protected $fillable = [
        'id_usuario',
        'id_punto',
        'id_producto',
        'fecha_novedad',
        'comentario_operario',
        'comentario_admin',
        'imagenes',
        'estado',
        'lote',
        'id_proveedor'
    ];

    protected $casts = [
        'imagenes' => 'array',
        'fecha_novedad' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function punto()
    {
        return $this->belongsTo(Puntos::class, 'id_punto');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'id_producto');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
}
