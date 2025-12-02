<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProveedorProducto extends Model
{
    protected $table = 'proveedores_producto';
    
    protected $guarded = [];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'id_producto');
    }
}
