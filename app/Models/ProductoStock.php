<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoStock extends Model
{
    protected $table = 'productos_stock';

    protected $fillable = [
        'producto_id',
        'stock_actual',
    ]; 
    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id', 'id');
    }
}
