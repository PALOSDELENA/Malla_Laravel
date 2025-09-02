<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Puntos;
use Illuminate\Http\Request;

class PuntosApiController extends Controller
{
    public function index()
    {
        $puntos = Puntos::all();

        return response()->json([
            "Puntos"=> $puntos
        ]);
    }

    public function store(Request $request)
    {
        $punto = Puntos::create([
            'nombre' => $request->input('nombre')
        ]);

        return response()->json([
            'message' => 'Punto registrado.',
            'Punto'=> $punto
        ], 201);
    }
}
