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

    // public function producciones()
    // {
    //     return $this->belongsToMany(Producciones::class, 'produccion_m_prima', 'm_prima_id','id')
    //         ->withPivot('cantidad');
    // }


    public function usosEnProduccion()
    {
        return $this->hasMany(ProduccioneProducto::class, 'm_prima_id');
    }
}