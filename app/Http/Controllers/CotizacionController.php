<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Puntos;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('cotizaciones.index');
    }

    public function create()
    {
        $productos = Productos::whereIn('proTipo', ['Carta-E', 'Carta-F', 'Carta-P', 'Carta-B'])->get();
        $sedes = Puntos::whereNotIn('nombre', ['Planta', 'Administrativo', 'Cocina', 'Parrilla'])->get();
        return view('cotizaciones.create', compact('productos', 'sedes'));
    }
}
