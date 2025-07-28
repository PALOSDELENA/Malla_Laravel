<?php

namespace App\Http\Controllers;

use App\Models\ProduccioneProducto;
use App\Models\Producciones;
use Illuminate\Http\Request;

class ProduccionController extends Controller
{
    public function index()
    {
        // $producciones = Producciones::orderBy('id', 'asc')->paginate(10);
        $producciones = Producciones::with('productos')->paginate(10);
        $materiasPrimas = \App\Models\Productos::where('proTipo', 'Materia Prima')->get();
        return view('admin_produccion.produccion', compact('producciones', 'materiasPrimas'));
    }

    public function create()
    {
        $materiasPrimas = \App\Models\Productos::where('proTipo', 'Materia Prima')->get();
        return view('admin_produccion.create', compact('materiasPrimas'));
    }   

    public function store(Request $request)
    {
        $validated = $request->validate([
            'produccion' => 'required|string|max:255',
            'tiempo_min' => 'required|numeric|min:0',
            'materias_primas' => 'required|array|min:1',
            'materias_primas.*' => 'required|integer|exists:productos,id',
        ]);

        // 1. Crear la producción
        $produccion = Producciones::create([
            'produccion' => $request->input('produccion'),
            'tiempo_min' => $request->input('tiempo_min'),
        ]);

        // 2. Guardar las materias primas relacionadas
        $materiasPrimas = $request->input('materias_primas');

        foreach ($materiasPrimas as $index => $materiaPrimaId) {
            ProduccioneProducto::create([
                'produccion_id' => $produccion->id,
                'm_prima_id' => $materiaPrimaId,
            ]);
        }

        return redirect()->route('producciones.index')->with('success', 'Producción registrada correctamente.');
    }

public function update(Request $request, $id)
{
    $request->validate([
        'produccion' => 'required|string|max:255',
        'materias_primas' => 'nullable|array',
        'materias_primas.*' => 'exists:productos,id',
    ]);

    // Buscar la producción
    $produccion = Producciones::findOrFail($id);

    // Actualizar nombre de la producción
    $produccion->produccion = $request->input('produccion');
    $produccion->save();

    // Sincronizar materias primas (relación muchos a muchos)
    if ($request->has('materias_primas')) {
        $produccion->productos()->sync($request->input('materias_primas'));
    } else {
        // Si no se envían materias primas, eliminamos todas las relaciones
        $produccion->productos()->sync([]);
    }

    return redirect()->route('producciones.index')->with('success', 'Producción actualizada correctamente.');
}    
    public function destroy($id)
    {
        $produccion = \App\Models\Producciones::findOrFail($id);
        $produccion->delete();

        return redirect()->route('producciones.index')->with('success', 'Producción eliminada correctamente.');
    }
}
