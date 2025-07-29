<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\TrazabilidadProducto;
use App\Models\User;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use App\Models\ProductoStock;
use Illuminate\Support\Facades\DB;

class TrazabilidadController extends Controller
{
    public function index()
    {
        $movimientos = TrazabilidadProducto::with(['producto', 'responsable'])
            ->orderBy('traFechaMovimiento', 'desc')
            ->paginate(10);
        $productos = Productos::orderBy('proNombre')->get();
        $usuarios = User::orderBy('usu_nombre')->get();

        return view('admin_trazabilidad.trazabilidad', compact('movimientos', 'productos', 'usuarios'));
    }
    public function create()
    {
        $productos = Productos::orderBy('proNombre')->get();
        $usuarios = User::orderBy('usu_nombre')->get();

        return view('admin_trazabilidad.create', compact('productos', 'usuarios'));
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
            'materias_primas.*.cantidad_real' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Verificar stock suficiente usando ProductoStock
            foreach ($validated['materias_primas'] as $materia) {
                $productoId = $materia['producto_id'];
                $cantidadSolicitada = $materia['cantidad_real'];

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
                TrazabilidadProducto::create([
                    'traFechaMovimiento'   => now()->toDateString(),
                    'traTipoMovimiento'    => 'Consumo Interno',
                    'traIdProducto'        => $materia['producto_id'],
                    'traCantidad'          => $materia['cantidad_real'],
                    'traLoteSerie'         => 'N/A',
                    'traProveedor'         => 'Producción',
                    'traDestino'           => 'Planta',
                    'traResponsable'       => $validated['responsable'],
                    'traColor'             => 'Bueno',
                    'traTextura'           => 'Bueno',
                    'traOlor'              => 'Bueno',
                    'traObservaciones'     => 'Materia prima utilizada en producción',
                    'orden_produccion_id'  => $orden->id,
                ]);
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
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'traFechaMovimiento' => 'required|date',
            'traTipoMovimiento' => 'required|string|in:Ingreso,Egreso,Devolución',
            'traIdProducto' => 'required|exists:productos,id',
            'traCantidad' => 'required|numeric|min:0',
            'traLoteSerie' => 'required|string|max:255',
            'traDestino' => 'required|string|max:255',
            'traResponsable' => 'required|exists:users,num_doc',
            'traColor' => 'required|in:Bueno,Malo',
            'traTextura' => 'required|in:Bueno,Malo',
            'traOlor' => 'required|in:Bueno,Malo',
            'traObservaciones' => 'nullable|string|max:500',
        ]);

        $traza = TrazabilidadProducto::findOrFail($id);
        $traza->update($request->all());

        return redirect()->route('trazabilidad.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $traza = TrazabilidadProducto::findOrFail($id);
        $traza->delete();

        return redirect()->route('trazabilidad.index')->with('success', 'Registro eliminado correctamente.');
    }
}
