<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("tipo_documento")->insert([
            [
            'id' => 1,
            'tipo_documento' => 'Cédula de Ciudadanía',
            ],
            [
            'id' => 2,
            'tipo_documento' => 'PPT',
            ]
        ]);
    }
}
