<?php

namespace App\Http\Controllers;

use App\Models\OrdenProduccion;
use App\Models\Producciones;
use App\Models\ProductoStock;
use App\Models\TrazabilidadProducto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenProduccionController extends Controller
{
    public function index()
    {
        // Aquí puedes implementar la lógica para mostrar la lista de órdenes de producción
        $ordenes = OrdenProduccion::with(['responsable1', 'producciones'])->paginate(10);
        $responsables = User::pluck('usu_nombre', 'num_doc');
        return view('admin_ordenProduccion.ordenPro', compact('ordenes', 'responsables'));
    }

    public function create()
    {
        $responsables = User::whereHas('cargo', function ($query) {
            $query->where('id', 2); // el ID del cargo deseado
        })->pluck('usu_nombre', 'num_doc');
        $producciones = Producciones::with('productos')->get();
        return view('admin_ordenProduccion.create', compact('responsables', 'producciones'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'responsable' => 'required|exists:users,num_doc',
            'produccion_id' => 'required|exists:producciones,id',
            'cantidad' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|string|max:50',
            'novedadProduccion' => 'nullable|string|max:255',

            'materias_primas' => 'required|array|min:1',
            'materias_primas.*.producto_id' => 'required|exists:productos,id',
            'materias_primas.*.cantidad_consumida' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Verificar stock suficiente usando ProductoStock
            foreach ($validated['materias_primas'] as $materia) {
                $productoId = $materia['producto_id'];
                $cantidadSolicitada = $materia['cantidad_consumida'];

                $stock = ProductoStock::where('producto_id', $productoId)->value('stock_actual') ?? 0;

                if ($cantidadSolicitada > $stock) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors([
                            'materias_primas' => "Stock insuficiente para el producto ID $productoId. Disponible: $stock, requerido: $cantidadSolicitada."
                        ]);
                }
            }

            // Crear la orden de producción
            $orden = OrdenProduccion::create([
                'responsable' => $validated['responsable'],
                'produccion_id' => $validated['produccion_id'],
                'cantidad' => $validated['cantidad'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'] ?? null,
                'estado' => $validated['estado'],
                'novedadProduccion' => $validated['novedadProduccion'] ?? null,
            ]);

            // Registrar cada consumo como trazabilidad
            foreach ($validated['materias_primas'] as $materia) {
                $productoId = $materia['producto_id'];
                $cantidadSolicitada = $materia['cantidad_consumida'];

                $stock = ProductoStock::where('producto_id', $productoId)->value('stock_actual') ?? 0;

                // if ($stock < $cantidadSolicitada) {
                //     return ('No hay suficiente stock para el producto ID ' . $productoId);
                    // return (redirect()->back()->withInput()->withErrors([
                    //     'materias_primas' => "Stock insuficiente para el producto ID $productoId. Disponible: $stock, requerido: $cantidadSolicitada."
                    // ]));
                // }

                // 1. Registrar el movimiento en trazabilidad
                TrazabilidadProducto::create([
                    'traFechaMovimiento' => now()->toDateString(),
                    'traTipoMovimiento' => 'Consumo Interno',
                    'traIdProducto' => $productoId,
                    'traCantidad' => $cantidadSolicitada,
                    'traLoteSerie' => 'N/A',
                    'traProveedor' => 'Producción',
                    'traDestino' => 'Planta',
                    'traResponsable' => $validated['responsable'],
                    'traColor' => 'Bueno',
                    'traTextura' => 'Bueno',
                    'traOlor' => 'Bueno',
                    'traObservaciones' => 'Materia prima utilizada en producción',
                    'orden_produccion_id' => $orden->id,
                ]);

                // 2. Descontar del stock actual
                ProductoStock::where('producto_id', $productoId)->decrement('stock_actual', $cantidadSolicitada);
            }

            DB::commit();

            return redirect()->route('ordenProduccion.index')
                ->with('success', 'Orden de producción registrada correctamente con su trazabilidad.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()]);
        }
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

        $request->validate([
            'responsable' => 'required',
            'cantidad' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'estado' => 'required|string|max:50',
            'novedadProduccion' => 'nullable|string|max:255',
        ]);
        

        // Actualizar consumos
        // $orden->consumoMateriaPrima()->delete(); // borrar anteriores
        // if ($request->has('materias_primas')) {
        //     foreach ($request->materias_primas as $consumo) {
        //         $orden->consumoMateriaPrima()->create([
        //             'traIdProducto' => $consumo['producto_id'],
        //             'traCantidad' => $consumo['cantidad_real'],
        //         ]);
        //     }
        // }

        
        $cantidadAnterior = $orden->consumoMateriaPrima()->traCantidad;
        dd($cantidadAnterior);

        $orden->update(attributes: $request->all());

        return redirect()->route('ordenProduccion.index')->with('success', 'Orden actualizada.');
    }

    public function destroy($id)
    {
        $orden = OrdenProduccion::findOrFail($id);
        $orden->consumoMateriaPrima()->delete();
        $orden->delete();

        return redirect()->route('ordenProduccion.index')->with('success', 'Orden eliminada.');
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
                'unidad' => $producto->proUnidadMedida,
                'cantidad' => $producto->pivot->cantidad_requerida ?? 0,
            ];
        });

        return response()->json($materias);
    }

    public function getConsumosMateriasPrimas($ordenId)
    {
        $trazas = TrazabilidadProducto::with('producto')
            ->where('orden_produccion_id', $ordenId)
            ->get();

        $materias = $trazas->map(function ($traza) {
            return [
                'id'       => $traza->traIdProducto,
                'nombre'   => $traza->producto->proNombre ?? 'N/A',
                'cantidad' => $traza->traCantidad,
            ];
        });

        return response()->json($materias);
    }
}
