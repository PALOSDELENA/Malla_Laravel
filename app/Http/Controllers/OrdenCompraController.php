<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\Productos;
use App\Models\Puntos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use TCPDF;


class OrdenCompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Usuario logueado
        $user = Auth::user();
        $punto = $user->punto->nombre ?? null;
        $id_punto = $user->usu_punto ?? null;

        $query = OrdenCompra::query();

        // Filtrar por punto del usuario
        if ($punto != 'Planta' && $punto != 'Administrativo')
        {
            $query->where('punto_id', $id_punto);
        }

        // Si vienen ambas fechas, usamos whereBetween
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_entrega_1', [$request->fecha_inicio, $request->fecha_fin]);
        } 
        // Si solo viene fecha_inicio
        elseif ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_entrega_1', '>=', $request->fecha_inicio);
        } 
        // Si solo viene fecha_fin
        elseif ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_entrega_1', '<=', $request->fecha_fin);
        }

        $ordenes = $query->orderBy('id', 'desc')->paginate(10);

        // Mantener filtros en paginación
        $ordenes->appends($request->all());

        return view('admin_ordenCompra.index', compact('ordenes', 'punto'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Lista de proveedores
        $proveedores = Productos::select('proFabricante')
            ->whereNotNull('proFabricante')
            ->where('proFabricante', '<>', '') // evita cadenas vacías
            ->distinct()
            ->orderBy('proFabricante')
            ->get();

        // Proveedor seleccionado desde el filtro
        $proveedorSeleccionado = $request->input('proveedor');

        $productoNombre = $request->input('producto');

        // Usuario logueado
        $user = Auth::user();
        $id_punto = $user->usu_punto ?? null;

        // Query base de productos
        $productosQuery = Productos::query();


        // Filtrar por proveedor si se selecciona uno
        if (!empty($proveedorSeleccionado)) {
            $productosQuery->where('proFabricante', $proveedorSeleccionado);
        }

        // Filtrar por nombre (LIKE %nombre%)
        if (!empty($productoNombre)) {
            $productosQuery->where('proNombre', 'like', '%' . $productoNombre . '%');
        }

        // Obtener productos paginados
        $productos = $productosQuery->paginate(10);
        $productos->appends($request->all()); //mantiene filtros en la paginación

        // Nombre del punto
        $punto = Puntos::where('id', $id_punto)->value('nombre');

        return view('admin_ordenCompra.crear', [
            'proveedores' => $proveedores,
            'productos' => $productos,
            'punto' => $punto,
            'id_punto' => $id_punto,
        ]);
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
                ->route('ordenCompra')
                ->with('success', 'Orden de compra creada correctamente.');

        } catch (\Exception $e) {
            // Registra el error en el log de Laravel
            Log::error('Error al crear la orden', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(), // datos que llegaron
            ]);
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
    // Devuelve la orden en JSON por ID
    public function show($id)
    {
        $orden = OrdenCompra::find($id);

        if (!$orden) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        return response()->json($orden);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $orden = OrdenCompra::with('producto')->find($id);

        return view('admin_ordenCompra.edit', compact('orden'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. Validar datos básicos
        $validated = $request->validate([
            'responsable' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'fecha_entrega_1' => 'required|date',
            'fecha_entrega_2' => 'required|date',
            'productos' => 'required|array',
        ]);

        // 2. Buscar la orden
        $orden = OrdenCompra::findOrFail($id);

        // 3. Actualizar datos de la orden
        $orden->update([
            'responsable' => $validated['responsable'],
            'email' => $validated['correo'],
            'fecha_entrega_1' => $validated['fecha_entrega_1'],
            'fecha_entrega_2' => $validated['fecha_entrega_2'],
        ]);

        // 4. Preparar datos para la tabla pivote
        $productosSync = [];
        foreach ($validated['productos'] as $productoId => $productoData) {
            $productosSync[$productoId] = [
                'inventario'      => $productoData['inventario'] ?? 0,
                'cantidad_bodega' => $productoData['cantidad_bodega'] ?? 0,
                'sugerido'        => $productoData['sugerido'] ?? 0,
                'pedido_1'        => $productoData['pedido_1'] ?? 0,
                'pedido_2'        => $productoData['pedido_2'] ?? 0,
                'precio_total'    => $productoData['precio_total'] ?? 0,
                'observaciones'   => $productoData['observaciones'] ?? '',
                'stock_minimo'    => $productoData['stock_minimo'] ?? 0,
            ];
        }

        // 5. Actualizar la relación pivote
        $orden->producto()->sync($productosSync);

        // 6. Redirigir con mensaje
        return redirect()->route('ordenCompra')->with('success', 'Orden actualizada correctamente.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $orden = OrdenCompra::findOrFail($id);
        $orden->delete();

        return redirect()->route('ordenCompra')->with('success', 'Orden eliminada correctamente.');
    }

    public function revision(Request $request, $id)
    {
        // Validar los campos del formulario
        $validated = $request->validate([
            'estado'     => 'required|in:aprobada,denegada',
            'comentario' => 'required|string|max:1000',
        ]);

        // Buscar la orden
        $orden = OrdenCompra::find($id);

        if (!$orden) {
            return redirect()->back()->with('error', 'Orden no encontrada');
        }

        // Actualizar los datos
        $orden->estado     = $validated['estado'];
        $orden->comentario_admin = $validated['comentario'];
        $orden->save();

        // Redirigir con mensaje
        return redirect()->route('ordenCompra')
                        ->with('success', 'La orden fue revisada correctamente.');
    }

    public function verPDF($id)
    {
        // Obtener la orden
        $orden = OrdenCompra::with('producto')->findOrFail($id);

        // Crear PDF con TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

        $pdf->SetCreator('Sistema de Órdenes');
        $pdf->SetAuthor('Mi Empresa');
        $pdf->SetTitle('Orden de Compra #' . $orden->id);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Marca de agua (si tienes imagen)
        $pageWidth = $pdf->getPageWidth();
        $pageHeight = $pdf->getPageHeight();
        $pdf->SetAlpha(0.1);
        $pdf->StartTransform();
        $pdf->Rotate(45, $pageWidth/2, $pageHeight/2);
        if (file_exists(public_path('img/marca_agua.jpg'))) {
            $pdf->Image(public_path('img/marca_agua.jpg'), 50, $pageHeight/2-50, 120);
        }
        $pdf->StopTransform();
        $pdf->SetAlpha(1);

        // Logo
        if (file_exists(public_path('img/logo.jpg'))) {
            $pdf->Image(public_path('img/logo.jpg'), 10, 10, 30);
        }

        // Título
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 30, 'ORDEN DE COMPRA #' . $orden->id, 0, 1, 'C');

        // Info general
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(50, 6, 'Punto de Venta:', 0);
        $pdf->Cell(0, 6, $orden->punto->nombre ?? '-', 0, 1);

        $pdf->Cell(50, 6, 'Responsable:', 0);
        $pdf->Cell(0, 6, $orden->responsable, 0, 1);

        $pdf->Cell(50, 6, 'Fecha Entrega 1:', 0);
        $pdf->Cell(0, 6, $orden->fecha_entrega_1, 0, 1);

        $pdf->Cell(50, 6, 'Fecha Entrega 2:', 0);
        $pdf->Cell(0, 6, $orden->fecha_entrega_2, 0, 1);

        $pdf->Ln(10);

        // Encabezados tabla productos
        $pdf->SetFillColor(245, 192, 46);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 7, 'Producto', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Proveedor', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'U.M.', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Inv.', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Pedido 1', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Pedido 2', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Total', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Precio', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Notas', 1, 1, 'C', true);

        // Datos productos
        $pdf->SetFont('helvetica', '', 9);
        foreach ($orden->producto as $prod) {
            $pdf->Cell(40, 6, $prod->proNombre, 1);
            $pdf->Cell(25, 6, $prod->proFabricante, 1, 0, 'C');
            $pdf->Cell(15, 6, $prod->proUnidadMedida, 1, 0, 'C');
            $pdf->Cell(15, 6, $prod->pivot->inventario, 1, 0, 'C');
            $pdf->Cell(20, 6, $prod->pivot->pedido_1, 1, 0, 'C');
            $pdf->Cell(20, 6, $prod->pivot->pedido_2, 1, 0, 'C');
            $pdf->Cell(20, 6, $prod->pivot->total_pedido, 1, 0, 'C');
            $pdf->Cell(20, 6, number_format($prod->pivot->precio_total, 2), 1, 0, 'R');
            $pdf->Cell(15, 6, $prod->pivot->observaciones, 1, 1);
        }

        // Salida
        return response($pdf->Output('orden_compra_'.$orden->id.'.pdf', 'I'))
               ->header('Content-Type', 'application/pdf');
    }
}
