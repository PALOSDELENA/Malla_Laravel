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

    /**
     * Get a single client (used by AJAX modal).
     */
    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return response()->json($cliente);
    }

    /**
     * Update an existing client (used by AJAX modal).
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'correo' => 'nullable|email|max:60',
        ]);

        $cliente->update($data);

        return response()->json($cliente);
    }
}
