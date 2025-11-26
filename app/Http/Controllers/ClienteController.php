<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    /**
     * Store a newly created client (used by AJAX modal).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'correo' => 'nullable|email|max:60',
        ]);

        $cliente = Cliente::create($data);

        return response()->json($cliente, 201);
    }
}
