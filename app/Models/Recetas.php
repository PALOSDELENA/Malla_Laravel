<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recetas extends Model
{
    protected $table = "palosdelena.recetas";

    protected $guarded = [];

    public $timestamps = false;
}
