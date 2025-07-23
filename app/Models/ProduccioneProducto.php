<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduccioneProducto extends Model
{

    protected $table = 'produccion_m_prima';

    protected $fillable = [
        'produccion_id',
        'm_prima_id',
        'cantidad',
    ];

    public $timestamps = false;
    public function producto()
    {
        return $this->belongsTo(Productos::class, 'm_prima_id');
    }

    public function produccion()
    {
        return $this->belongsTo(Producciones::class, 'produccion_id');
    }
}
