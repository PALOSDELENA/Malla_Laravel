<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $connection = 'mysql-EC2';
    protected $table = "productos";

    protected $guarded = [];

    public $timestamps = false;


    public function productosTra()
    {
        return $this->hasMany(TrazabilidadProducto::class, 'traIdProducto', 'id');
    }
}
