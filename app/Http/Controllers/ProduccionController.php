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
        'cantidad' => 'nullable|array',
        'cantidad.*' => 'numeric|min:0',
    ]);

    $produccion = Producciones::findOrFail($id);
    $produccion->produccion = $request->input('produccion');
    $produccion->save();

    if ($request->has('materias_primas')) {
        $materiasPrimas = $request->input('materias_primas');
        $cantidades = $request->input('cantidad');

        $syncData = [];
        foreach ($materiasPrimas as $index => $productoId) {
            $cantidad = $cantidades[$index] ?? 0;
            $syncData[$productoId] = ['cantidad_requerida' => $cantidad];
        }

        $produccion->productos()->sync($syncData);
    } else {
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
