<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrazabilidadProducto extends Model
{
    protected $connection = 'mysql-EC2';
    protected $table = "trazabilidadProductos";

    protected $guarded = [];

    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'traIdProducto', 'id');
    }
}
