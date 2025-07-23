<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrazabilidadProducto extends Model
{
    protected $table = "trazabilidadProductos";

    protected $guarded = [];

    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'traIdProducto', 'id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'traResponsable', 'num_doc');
    }
}
