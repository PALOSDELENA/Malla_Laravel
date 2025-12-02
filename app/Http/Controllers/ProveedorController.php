<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Proveedor;
use App\Models\ProveedorProducto;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProveedorController extends Controller
{
    public function index()
    {
        $insumos = Productos::all();
        $proveedores = Proveedor::all();

        // Paginar por novedades (registros de la tabla pivote)
        $novedades = DB::table('proveedores_producto as pp')
            ->join('proveedores as p', 'pp.id_proveedor', '=', 'p.id')
            ->join('productos as pr', 'pp.id_producto', '=', 'pr.id')
            ->select(
                'pp.*',
                'p.nombre as proveedor',
                'pr.proNombre as producto'
            )
            ->orderBy('pp.created_at', 'desc')
            ->paginate(10);

        return view('novedad_proveedor.index', compact('insumos', 'proveedores', 'novedades'));
    }

    // public function store(Request $request)
    // {
    //     // Validar los datos recibidos del formulario
    //     $validated = $request->validate([
    //         'id_proveedor' => 'required|exists:proveedores,id',
    //         'id_producto' => 'required|exists:productos,id',
    //         'calidad_producto' => 'required|in:excelente,aceptable,bueno,malo',
    //         'tiempo_entrega' => 'required|in:excelente,aceptable,bueno,malo',
    //         'presentacion_personal' => 'required|in:excelente,aceptable,bueno,malo',
    //         'observacion' => 'nullable|string|max:1000',
    //     ]);

    //     // Buscar el proveedor
    //     $proveedor = Proveedor::findOrFail($validated['id_proveedor']);

    //     // Registrar la novedad (insertar o actualizar el registro en la tabla pivote)
    //     // Si el proveedor ya tiene ese producto, se actualizan los campos del pivote.
    //     $proveedor->productosNovedad()->syncWithoutDetaching([
    //         $validated['id_producto'] => [
    //             'calidad_producto' => $validated['calidad_producto'],
    //             'tiempo_entrega' => $validated['tiempo_entrega'],
    //             'presentacion_personal' => $validated['presentacion_personal'],
    //             'observacion' => $validated['observacion'] ?? null,
    //         ]
    //     ]);

    //     // Mensaje de éxito (redirigir a la vista anterior o a la lista)
    //     return redirect()->back()->with('success', 'Novedad registrada correctamente.');
    // }

    public function store(Request $request)
    {
        // Validar los datos recibidos del formulario
        $validated = $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id',
            'id_producto' => 'required|exists:productos,id',
            'calidad_producto' => 'required|in:excelente,aceptable,bueno,malo',
            'tiempo_entrega' => 'required|in:excelente,aceptable,bueno,malo',
            'presentacion_personal' => 'required|in:excelente,aceptable,bueno,malo',
            'observacion' => 'nullable|string|max:1000',
        ]);

        // Crear nuevo registro sin importar si ya existe otro igual
        ProveedorProducto::create([
            'id_proveedor' => $validated['id_proveedor'],
            'id_producto' => $validated['id_producto'],
            'calidad_producto' => $validated['calidad_producto'],
            'tiempo_entrega' => $validated['tiempo_entrega'],
            'presentacion_personal' => $validated['presentacion_personal'],
            'observacion' => $validated['observacion'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Novedad registrada correctamente.');
    }
    
    public function getProducts($id)
    {
        $insumos = Productos::where('id_proveedor', $id)
            ->select('id', 'proNombre')
            ->get();

        return response()->json($insumos);
    }

    public function filtrarFetch(Request $request)
    {
        $proveedor = $request->input('proveedor');
        $producto = $request->input('producto');

        $query = Proveedor::with(['productosNovedad' => function ($q) use ($producto) {
            if ($producto) {
                $q->where('proNombre', 'like', "%{$producto}%");
            }
        }]);

        if ($proveedor) {
            $query->where('nombre', 'like', "%{$proveedor}%");
        }

        $novedades = $query->get();

        // Formatear los datos para enviar al front
        $result = $novedades->map(function ($prov) {
            return [
                'proveedor' => $prov->nombre,
                'productos' => $prov->productosNovedad->map(function ($prod) {
                    return [
                        'producto' => $prod->proNombre,
                        'calidad_producto' => $prod->pivot->calidad_producto,
                        'tiempo_entrega' => $prod->pivot->tiempo_entrega,
                        'presentacion_personal' => $prod->pivot->presentacion_personal,
                        'observacion' => $prod->pivot->observacion,
                        'fecha' => optional($prod->pivot->created_at)->format('d-m-Y'),
                    ];
                }),
            ];
        });

        return response()->json($result);
    }

    public function filtrar(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        // Insumos (productos para el modal)
        $insumos = \App\Models\Productos::all();
        $proveedores = Proveedor::all();

        // Consultar los registros pivote filtrados por fecha y paginar por novedades
        $query = DB::table('proveedores_producto as pp')
            ->join('proveedores as p', 'pp.id_proveedor', '=', 'p.id')
            ->join('productos as pr', 'pp.id_producto', '=', 'pr.id')
            ->select(
                'pp.*',
                'p.nombre as proveedor',
                'pr.proNombre as producto'
            )
            ->orderBy('pp.created_at', 'desc');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('pp.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        }

        $novedades = $query->paginate(10);

        return view('novedad_proveedor.index', compact('novedades', 'insumos', 'proveedores', 'fechaInicio', 'fechaFin'));
    }

    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if (!$fechaInicio || !$fechaFin) {
            return redirect()->back()->with('error', 'Debe seleccionar un rango de fechas.');
        }

        // Consultar los registros filtrados por fecha
        $novedades = DB::table('proveedores_producto as pp')
            ->join('proveedores as p', 'pp.id_proveedor', '=', 'p.id')
            ->join('productos as pr', 'pp.id_producto', '=', 'pr.id')
            ->select(
                'p.nombre as proveedor',
                'pr.proNombre as producto',
                'pp.calidad_producto',
                'pp.tiempo_entrega',
                'pp.presentacion_personal',
                'pp.observacion',
                'pp.created_at'
            )
            ->whereBetween('pp.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->orderBy('pp.created_at', 'desc')
            ->get();

        // Crear el Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

    // Título principal (fila 1) que ocupa de A a G
    $sheet->mergeCells('A1:G1');
    $sheet->setCellValue('A1', 'Reporte de Novedades Proveedor');
    // Título en negrita, centrado y mayor tamaño
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Encabezados (fila 2)
    $sheet->setCellValue('A2', 'Proveedor');
    $sheet->setCellValue('B2', 'Producto');
    $sheet->setCellValue('C2', 'Calidad Producto');
    $sheet->setCellValue('D2', 'Tiempo de Entrega');
    $sheet->setCellValue('E2', 'Presentación Personal');
    $sheet->setCellValue('F2', 'Observación');
    $sheet->setCellValue('G2', 'Fecha Registro');

    // Formato en negrita y centrado para los encabezados
    $sheet->getStyle('A2:G2')->getFont()->setBold(true);
    $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Insertar los datos a partir de la fila 3
    $row = 3;
        foreach ($novedades as $nov) {
            $sheet->setCellValue("A{$row}", $nov->proveedor);
            $sheet->setCellValue("B{$row}", $nov->producto);
            $sheet->setCellValue("C{$row}", ucfirst($nov->calidad_producto));
            $sheet->setCellValue("D{$row}", ucfirst($nov->tiempo_entrega));
            $sheet->setCellValue("E{$row}", ucfirst($nov->presentacion_personal));
            $sheet->setCellValue("F{$row}", $nov->observacion);
            $sheet->setCellValue("G{$row}", $nov->created_at);
            $row++;
        }

        // Ajustar ancho de columnas automáticamente
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generar el archivo para descarga
        $fileName = 'Reporte_Novedades_' . now()->format('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }
}
