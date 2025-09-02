<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'inventario_secciones';

    protected $fillable = [
        'nobre',
    ];

    public $timestamps = false;

    public function productos()
    {
        return $this->hasMany(Productos::class, 'proSeccion', 'id');
    }
}
