<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = "encuestas.encuestas_satisfaccion";

    protected $guarded = [];

    public $timestamps = false;
}
