<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\Productos;
use App\Models\Puntos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class OrdenCompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = OrdenCompra::paginate(10);
        return view('admin_ordenCompra.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $proveedores = Productos::select('proFabricante')
            ->distinct()
            ->orderBy('proFabricante')
            ->get();

        $productos = Productos::paginate(10);

        $user = Auth::user(); // usuario logueado
        
        $id_punto = $user->usu_punto ?? null;

        $punto = Puntos::where('id', $id_punto)->value('nombre');

        return view('admin_ordenCompra.crear', compact('proveedores', 'productos', 'punto', 'id_punto')); 
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    try {
        $request->validate([
            'punto_id' => 'required|integer|exists:puntos,id',
            'responsable' => 'required|string|max:255',
            'correo' => 'required|email',
            'fecha_entrega_1' => 'required|date',
            'fecha_entrega_2' => 'required|date|after_or_equal:fecha_entrega_1',
        ]);

        // Crear la orden
        $orden = OrdenCompra::create([
            'responsable'      => $request->responsable,
            'punto_id'         => $request->punto_id,
            'email'           => $request->correo,
            'fecha_entrega_1'  => $request->fecha_entrega_1,
            'fecha_entrega_2'  => $request->fecha_entrega_2,
            'estado'           => 'Pendiente',
        ]);

        // Adjuntar productos a la orden
        if ($request->has('productos')) {
            foreach ($request->productos as $producto) {
                $pedido1 = $producto['pedido_1'] ?? 0;
                $pedido2 = $producto['pedido_2'] ?? 0;

                if ($pedido1 > 0 || $pedido2 > 0) {
                    $orden->producto()->attach($producto['id'], [
                        'inventario'      => $producto['inventario'] ?? 0,
                        'sugerido'        => $producto['sugerido'] ?? 0,
                        'pedido_1'        => $pedido1,
                        'pedido_2'        => $pedido2,
                        'total_pedido'    => $producto['total_pedido'] ?? 0,
                        'precio_total'    => $producto['precio_total'] ?? 0,
                        'observaciones'   => $producto['observaciones'] ?? null,
                        'cantidad_bodega' => $producto['cantidad_bodega'] ?? 0,
                        'stock_minimo'    => $producto['stock_minimo'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()
            ->route('crearOrden')
            ->with('success', 'Orden de compra creada correctamente.');

    } catch (\Exception $e) {
        // Registra el error en el log de Laravel
        Log::error('Error al crear la orden: ' . $e->getMessage());

        // Redirige con un mensaje de error
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Ocurrió un error al crear la orden: ' . $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(OrdenCompra $ordenCompra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrdenCompra $ordenCompra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrdenCompra $ordenCompra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrdenCompra $ordenCompra)
    {
        //
    }
}
