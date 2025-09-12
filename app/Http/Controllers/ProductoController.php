<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\ProductoStock;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filtros desde el formulario
        $nombre = $request->input('proNombre');
        $unidad = $request->input('proUnidadMedida');
        // $secciones = ;

        // Consulta con filtros y paginación
        $productos = Productos::query()
            ->when($nombre, fn($q) => $q->where('proNombre', 'like', "%{$nombre}%"))
            ->when($unidad, fn($q) => $q->where('proUnidadMedida', 'like', "%{$unidad}%"))
            ->orderBy('id', 'desc')
            ->paginate(10) // Cambia la cantidad de registros por página si lo deseas
            ->withQueryString(); // mantiene filtros al paginar

        return view('admin_items.items', compact('productos', 'nombre', 'unidad'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin_items.createItem');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los campos
        $validated = $request->validate([
            'proNombre' => 'required|string|max:255',
            'proUnidadMedida' => 'required|string|max:50',
            'proTipo' => 'required|string|in:Materia Prima,Producto Terminado',
            'proListaIngredientes' => 'nullable|string',
            'proCondicionesConservacion' => 'nullable|string|max:255',
            'proFabricante' => 'nullable|string|max:255',
            'proPrecio' => 'nullable',
        ]);

        // Procesar ingredientes (opcional: guardarlos como JSON o string)
        $ingredientes = $validated['proListaIngredientes'] ?? '';
        
        // Crea el producto
        $producto = Productos::create([
            'proNombre' => $validated['proNombre'],
            'proUnidadMedida' => $validated['proUnidadMedida'],
            'proTipo' => $validated['proTipo'],
            'proListaIngredientes' => $ingredientes,
            'proCondicionesConservacion' => $validated['proCondicionesConservacion'] ?? null,
            'proFabricante' => $validated['proFabricante'] ?? null,
            'proPrecio' => $validated['proPrecio'] ?? null,
        ]);

        // Redirecciona o responde
        // return redirect()->route('productos.index')->with('success', 'Producto registrado correctamente.');    }
        return redirect()->back()->with('success', 'Producto registrado correctamente.');
    }
    /**
     * Display the specified resource.
     */
    public function show(Productos $productos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Productos $productos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Productos $producto)
    {
        $validated = $request->validate([
            'proNombre' => 'required|string|max:255',
            'proUnidadMedida' => 'required|string',
            'proTipo' => 'required|string',
            'proListaIngredientes' => 'nullable|string',
            'proCondicionesConservacion' => 'nullable|string',
            'proFabricante' => 'nullable|string',
            'proPrecio' => 'nullable',
            'proSeccion' => 'nullable',
        ]);

        // Asignación uno por uno
        $producto->proNombre = $request->input('proNombre');
        $producto->proUnidadMedida = $request->input('proUnidadMedida');
        $producto->proTipo = $request->input('proTipo');
        $producto->proListaIngredientes = $request->input('proListaIngredientes');
        $producto->proCondicionesConservacion = $request->input('proCondicionesConservacion');
        $producto->proFabricante = $request->input('proFabricante');
        $producto->proPrecio = $request->input('proPrecio');
        $producto->proSeccion = $request->input('proSeccion');

        $producto->save();
        
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Productos $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function stockChart()
    {
        $stocks = ProductoStock::with('producto')->get();

        $labels = $stocks->pluck('producto.proNombre');
        $cantidades = $stocks->pluck('stock_actual');

        return view('admin_items.existencias', compact('labels', 'cantidades'));
    }
}
