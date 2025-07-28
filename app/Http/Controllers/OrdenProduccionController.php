<?php

namespace App\Http\Controllers;

use App\Models\OrdenProduccion;
use App\Models\Producciones;
use App\Models\TrazabilidadProducto;
use App\Models\User;
use Illuminate\Http\Request;

class OrdenProduccionController extends Controller
{
    public function index()
    {
        // Aquí puedes implementar la lógica para mostrar la lista de órdenes de producción
        $ordenes = OrdenProduccion::with(['responsable1', 'producciones'])->get();
        return view('admin_ordenProduccion.ordenPro', compact('ordenes'));
    }

    public function create()
    {
        $responsables = User::pluck('usu_nombre', 'num_doc');
        $producciones = Producciones::with('productos')->get();
        return view('admin_ordenProduccion.create', compact('responsables', 'producciones'));
    }

    public function store(Request $request)
    {
        // Validación básica
        $validated = $request->validate([
            'responsable' => 'required|string',
            'produccion_id' => 'required|exists:producciones,id',
            'cantidad' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|string|max:50',
            'novedadProduccion' => 'nullable|string|max:255',
            'materias_primas' => 'required|array',
            'materias_primas.*.producto_id' => 'required|exists:productos,id',
            'materias_primas.*.cantidad_real' => 'required|numeric|min:0',
        ]);

        // 1. Crear orden de producción
        $orden = OrdenProduccion::create([
            'responsable' => $validated['responsable'],
            'produccion_id' => $validated['produccion_id'],
            'cantidad' => $validated['cantidad'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'estado' => $validated['estado'],
            'novedadProduccion' => $validated['novedadProduccion'],
        ]);

        // 2. Registrar trazabilidad de cada materia prima usada
        foreach ($validated['materias_primas'] as $materia) {
            TrazabilidadProducto::create([
                'traFechaMovimiento' => now()->toDateString(),
                'traTipoMovimiento' => 'Consumo Interno',
                'traIdProducto' => $materia['producto_id'],
                'traCantidad' => $materia['cantidad_real'],
                'traLoteSerie' => 'N/A', // Puedes ajustar si lo tomas desde un input
                'traProveedor' => 'Producción', // o puedes usar null si no aplica
                'traDestino' => 'Planta', // o lo que corresponda
                'traResponsable' => $validated['responsable'],
                'traColor' => 'Bueno', // Puedes parametrizar esto también si viene del form
                'traTextura' => 'Bueno',
                'traOlor' => 'Bueno',
                'traObservaciones' => 'Materia prima utilizada en producción',
                'orden_produccion_id' => $orden->id,
            ]);
        }

        return redirect()->route('ordenProduccion.index')->with('success', 'Orden de Producción y trazabilidad registradas.');
    }

    public function edit($id)
    {
        $orden = OrdenProduccion::with('consumoMateriaPrima')->findOrFail($id);
        $responsables = User::pluck('usu_nombre', 'num_doc');
        $producciones = Producciones::pluck('produccion', 'id');

        return view('orden_produccion.edit', compact('orden', 'responsables', 'producciones'));
    }

    public function update(Request $request, $id)
    {
        $orden = OrdenProduccion::findOrFail($id);

        $data = $request->validate([
            'responsable' => 'required',
            'produccion_id' => 'required|exists:producciones,id',
            'cantidad' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'estado' => 'required|string|max:50',
            'novedadProduccion' => 'nullable|string|max:255',
        ]);

        $orden->update($data);

        // Actualizar consumos
        $orden->consumoMateriaPrima()->delete(); // borrar anteriores
        if ($request->has('materias_primas')) {
            foreach ($request->materias_primas as $consumo) {
                $orden->consumoMateriaPrima()->create([
                    'traIdProducto' => $consumo['producto_id'],
                    'traCantidad' => $consumo['cantidad_real'],
                ]);
            }
        }

        return redirect()->route('orden-produccion.index')->with('success', 'Orden actualizada.');
    }

    public function destroy($id)
    {
        $orden = OrdenProduccion::findOrFail($id);
        $orden->consumoMateriaPrima()->delete();
        $orden->delete();

        return redirect()->route('orden-produccion.index')->with('success', 'Orden eliminada.');
    }

    public function getMateriasPrimas($id)
    {
        $produccion = Producciones::with('productos')->find($id);

        if (!$produccion) {
            return response()->json([]);
        }

        $materias = $produccion->productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->proNombre,
            ];
        });

        return response()->json($materias);
    }
}
