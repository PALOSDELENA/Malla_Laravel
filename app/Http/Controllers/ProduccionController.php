<?php

namespace App\Http\Controllers;

use App\Models\ProduccioneProducto;
use App\Models\Producciones;
// use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'cantidad' => 'required|array',
            'cantidad.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Crear la producción
            $produccion = Producciones::create([
                'produccion' => $validated['produccion'],
                'tiempo_min' => $validated['tiempo_min'],
            ]);

            // 2. Guardar las materias primas con sus cantidades
            foreach ($validated['materias_primas'] as $index => $materiaPrimaId) {
                $cantidad = $validated['cantidad'][$index];

                ProduccioneProducto::create([
                    'produccion_id' => $produccion->id,
                    'm_prima_id' => $materiaPrimaId,
                    'cantidad_requerida' => $cantidad, 
                ]);
            }

            DB::commit();
            
            return redirect()->route('producciones.index')->with('success', 'Producción registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al registrar la producción: ' . $e->getMessage()]);
        }
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
        $produccion = Producciones::findOrFail($id);
        $produccion->delete();

        return redirect()->route('producciones.index')->with('success', 'Producción eliminada correctamente.');
    }
}
