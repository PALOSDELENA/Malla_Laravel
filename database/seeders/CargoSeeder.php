<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table("cargos")->insert([
        [
            'id' => 1,
            'car_nombre' => 'Administrador',
        ],
        [
            'id' => 2,
            'car_nombre' => 'Planta',
        ],
        [
            'id' => 3,
            'car_nombre' => 'Mesero',
        ],
        [
            'id' => 4,
            'car_nombre' => 'Gerente',
        ],
       ]);
    }
}
