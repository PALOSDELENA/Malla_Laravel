<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PuntosController extends Controller
{
    public function index()
    {
        $puntos = \App\Models\Puntos::orderBy('id', 'asc')->paginate(10);
        return view('admin_puntos.puntos', compact('puntos'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        \App\Models\Puntos::create($validated);

        return redirect()->route('puntos.index')->with('success', 'Punto creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $punto = \App\Models\Puntos::findOrFail($id);
        $punto->nombre = $validated['nombre'];
        $punto->save();

        return redirect()->route('puntos.index')->with('success', 'Punto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $punto = \App\Models\Puntos::findOrFail($id);
        $punto->delete();

        return redirect()->route('puntos.index')->with('success', 'Punto eliminado correctamente.');
    }
}
