<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoInfo extends Model
{
    protected $table = "encuestas.puntos";

    protected $guarded = [];

    public $timestamps = false;
}
