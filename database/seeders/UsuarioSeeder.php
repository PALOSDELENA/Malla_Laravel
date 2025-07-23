<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Paso 1: Insertar en 'usuarios'
        DB::table('users')->insert([
            't_doc' => 2, // 1: Cédula, 2: PPT
            'num_doc' => '4869681',
            'usu_nombre' => 'Yonathan',
            'usu_apellido' => 'Nieves',
            'usu_celular' => '3006290689',
            'usu_email' => 'yonathannieves17@gmail.com',
            'usu_comentario' => '',
            'usu_cargo' => 1, // 1: Administrador, 2: Planta, 3: Mesero, 4: Gerente
            'usu_punto' => 1, // 1: Puente Aranda
            'usu_estado' => 'Activo',
        ]);

        // Paso 2: Insertar en 'seguridad'
        DB::table('seguridad')->insert([
            'seg_credencial' => Hash::make('clave_segura'),
            'seg_usuario_id' => '4869681',
        ]);
    }
}
