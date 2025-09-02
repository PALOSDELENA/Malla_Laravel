<?php

namespace App\Http\Controllers;

use App\Models\OrdenProduccion;
use App\Models\Productos;
use App\Models\TrazabilidadProducto;
use App\Models\User;
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
        $request->validate([
            'traFechaMovimiento' => 'required|date',
            'traTipoMovimiento'  => 'required|string|in:Ingreso,Egreso,Consumo Interno',
            'traIdProducto'      => 'required|exists:productos,id',
            'traCantidad'        => 'required|numeric|min:0.01',
            'traLoteSerie'       => 'required|string|max:255',
            'traObservaciones'   => 'nullable|string|max:500',
            'traDestino'         => 'required|string',
            'traResponsable'     => 'required|exists:users,num_doc',
            'traColor'           => 'required|string',
            'traTextura'         => 'required|string',
            'traOlor'            => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Obtener o crear el stock actual
            $stock = ProductoStock::firstOrCreate(
                ['producto_id' => $request->traIdProducto],
                ['stock_actual' => 0]
            );

            // Validación y actualización de stock
            if ($request->traTipoMovimiento === 'Ingreso') {
                $stock->increment('stock_actual', $request->traCantidad);
            } elseif (in_array($request->traTipoMovimiento, ['Egreso', 'Consumo Interno'])) {
                if ($stock->stock_actual < $request->traCantidad) {
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['traCantidad' => 'Stock insuficiente para realizar este movimiento.']);
                }

                $stock->decrement('stock_actual', $request->traCantidad);
            }

            // Registrar trazabilidad
            TrazabilidadProducto::create($request->all());

            DB::commit();

            return redirect()
                ->route('trazabilidad.index')
                ->with('success', 'Movimiento registrado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Error al registrar movimiento de trazabilidad', [
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['general' => 'Ocurrió un error al registrar el movimiento.']);
        }
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'traCantidad' => 'required|numeric|min:0',
            'traLoteSerie' => 'required|string|max:255',
            'traDestino' => 'required|string|max:255',
            'traResponsable' => 'required|exists:users,num_doc',
            'traColor' => 'required|in:Bueno,Malo',
            'traTextura' => 'required|in:Bueno,Malo',
            'traOlor' => 'required|in:Bueno,Malo',
            'traObservaciones' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $traza = TrazabilidadProducto::findOrFail($id);

            $cantidadAnterior = $traza->traCantidad;
            $cantidadNueva = $request->traCantidad;

            $stock = ProductoStock::firstOrCreate(
                ['producto_id' => $traza->traIdProducto],
                ['stock_actual' => 0]
            );

            if ($cantidadAnterior !== $cantidadNueva) {
                if($cantidadNueva > $cantidadAnterior) {
                    // Si la nueva cantidad es mayor, se trata de un ingreso
                    $diferencia = $cantidadNueva - $cantidadAnterior;
                    $stock->increment('stock_actual', $diferencia);
                } else {
                    // Si la nueva cantidad es menor, se trata de un egreso
                    $diferencia = $cantidadAnterior - $cantidadNueva;
                    if ($stock->stock_actual < $diferencia) {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->withErrors(['traCantidad' => 'Stock insuficiente para realizar esta actualización.']);
                    }
                    $stock->decrement('stock_actual', $diferencia);
                }
            }

            $traza->update($request->all());

            DB::commit();

            return redirect()->route('trazabilidad.index')->with('success', 'Registro actualizado correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error al actualizar trazabilidad', [
                'error' => $e->getMessage(),
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['general' => 'Ocurrió un error al actualizar el registro.']);
        }
    }

    public function destroy($id)
    {
        $traza = TrazabilidadProducto::findOrFail($id);
        $tipoMovimiento = $traza->traTipoMovimiento;
        $stock = ProductoStock::where('producto_id', $traza->traIdProducto)->first();

        if ($tipoMovimiento === 'Ingreso') {
            $stock->decrement('stock_actual', $traza->traCantidad);
        } elseif (in_array($tipoMovimiento, ['Egreso', 'Consumo Interno'])) {
            $stock->increment('stock_actual', $traza->traCantidad);
        }
        
        $traza->delete();

        return redirect()->route('trazabilidad.index')->with('success', 'Registro eliminado correctamente.');
    }
}
