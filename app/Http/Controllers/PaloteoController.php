<?php

namespace App\Http\Controllers;

use App\Models\Inventario_Historico;
use App\Models\Productos;
use App\Models\Puntos;
use App\Models\Seccion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function Laravel\Prompts\select;

class PaloteoController extends Controller
{
    public function index()
    {
        return view('paloteo.index');    
    }

    public function obtenerPuntos()
    {
        $puntos = Puntos::all();

        return response()->json($puntos);
    }

    // public function obtenerGerente($punto, $fechaInicio, $fechaFin)
    public function obtenerGerente(Request $request)
    {
        $punto = $request->punto;
        $fechaInicio = $request->fechaInicio; // ya en formato Y-m-d
        $fechaFin    = $request->fechaFin;

        $gerente = Inventario_Historico::where('punto_id', $punto)
            ->where('fecha_inicio', $fechaInicio)
            ->where('fecha_fin', $fechaFin)
            ->first();

        if (!$gerente) {
            return response()->json([
                'success' => true,
                'nombre_encargado' => ''
            ]);
        }

        if ($gerente) {
            return response()->json([
                'success' => true,
                'nombre_encargado' => $gerente->encargado
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'No se encontró encargado para este punto.'
            ]);
        }
    }

    public function reporteSemanal($punto, $seccion)
    {
        $resultados = DB::table('productos as p')
            ->leftJoin('trazabilidadProductos as r', function($join) use ($punto){
                $join->on('p.id', '=', 'r.traIdProducto')
                     ->where('r.traPunto', '=', $punto)   // filtra por punto
                     ->whereRaw("YEARWEEK(r.traFechaMovimiento, 1) = YEARWEEK(CURRENT_DATE, 1)"); // solo semana actual
            })
            ->select(
                'p.id',
                'p.proNombre as nombre',
                DB::raw("COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 2 THEN r.traCantidad END), 0) as lunes"),
                DB::raw("COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 3 THEN r.traCantidad END), 0) as martes"),
                DB::raw("COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 4 THEN r.traCantidad END), 0) as miercoles"),
                DB::raw("COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 5 THEN r.traCantidad END), 0) as jueves"),
                DB::raw("COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 6 THEN r.traCantidad END), 0) as viernes"),
                DB::raw("COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 7 THEN r.traCantidad END), 0) as sabado"),
                DB::raw("COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 1 THEN r.traCantidad END), 0) as domingo")
            )
            ->where('p.proSeccion', $seccion) // filtra por sección
            ->groupBy('p.id', 'p.proNombre')
            ->orderBy('p.proNombre')
            ->get();

        return response()->json($resultados);
    }

    public function obtenerProductos()
    {
        $productos = DB::table('productos as p')
            ->join('inventario_secciones as s', 'p.proSeccion', '=', 's.id')
            ->select('p.id', 'p.proNombre as nombre', 's.id as seccion_id', 's.nombre as seccion_nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => (string) $item->id,
                    'nombre' => (string) $item->nombre,
                    'seccion_id' => (string) $item->seccion_id,
                    'seccion_nombre' => (string) $item->seccion_nombre,
                ];
            });
        return response()->json($productos);
    }

    public function getProductos()
    {
        $productos = DB::table('productos as p')
            ->select('p.id', 'p.proNombre as nombre')
            ->where('proSeccion', NULL)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => (string) $item->id,
                    'nombre' => (string) $item->nombre,
                ];
            });
        return response()->json($productos);
    }

    public function quitarSeccion($id)
    {
        $producto = Productos::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $producto->proSeccion = NULL; 
        $producto->save();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
    }
    
    public function asignarSeccion(Request $request, $id)
    {
        $producto = Productos::findOrFail($id);
        $producto->proSeccion = $request->input('seccion_id'); 
        $producto->save();

        // inventario de la sección después de actualizar
        $inventario = Productos::where('proSeccion', $request->input('seccion_id'))
            ->select('proNombre as nombre')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado con éxito',
            'producto' => $producto,
            'inventario' => $inventario
        ]);
    }

    public function guardarInventario(Request $request)
    {
        try {
            $data = $request->validate([
                'producto_id' => 'required|integer',
                'punto_id'    => 'required|integer',
                'dia'         => 'required|string',
                'cantidad'    => 'required|numeric',
            ]);

            $dias = [
                'lunes'      => Carbon::MONDAY,
                'martes'     => Carbon::TUESDAY,
                'miercoles'  => Carbon::WEDNESDAY,
                'jueves'     => Carbon::THURSDAY,
                'viernes'    => Carbon::FRIDAY,
                'sabado'     => Carbon::SATURDAY,
                'domingo'    => Carbon::SUNDAY,
            ];

            if (!isset($dias[$data['dia']])) {
                throw new \Exception('Día inválido');
            }

            // Inicio de semana (lunes actual)
            $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);

            // Calcular la fecha exacta para el día enviado
            $fecha = (clone $monday)->next($dias[$data['dia']]);
            if ($data['dia'] === 'lunes') {
                $fecha = $monday;
            }

            if ($data['cantidad'] == 0) {
                // Borrar si la cantidad es 0
                DB::table('trazabilidadProductos')
                    ->where('traIdProducto', $data['producto_id'])
                    ->where('traPunto', $data['punto_id'])
                    ->whereDate('traFechaMovimiento', $fecha->toDateString())
                    ->delete();

                $accion = 'eliminar';
            } else {
                // Insertar o actualizar
                DB::table('trazabilidadProductos')->updateOrInsert(
                    [
                        'traIdProducto' => $data['producto_id'],
                        'traPunto'    => $data['punto_id'],
                        'traTipoMovimiento' => 'Ajuste Paloteo',
                        'traFechaMovimiento'  => $fecha->toDateString(),
                    ],
                    [
                        'traCantidad'    => $data['cantidad'],
                    ]
                );

                $accion = 'guardar';
            }

            return response()->json([
                'success' => true,
                'debug' => [
                    'dia'            => $data['dia'],
                    'fecha_calculada'=> $fecha->toDateString(),
                    'dia_semana'     => $fecha->locale('es')->dayName,
                    'cantidad'       => $data['cantidad'],
                    'accion'         => $accion
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function obtenerHistorico(Request $request, $punto_id)
    {
        try {
            // Validar punto_id recibido
            // $punto_id = (int) $request->query('punto_id', 0);

            // if ($punto_id <= 0) {
            //     return response()->json(['error' => 'ID de punto inválido'], 400);
            // }

            // Consultar la base de datos
            $semanas = DB::table('inventario_historico')
                ->select('id', 'fecha_inicio', 'fecha_fin')
                ->where('punto_id', $punto_id)
                ->orderByDesc('fecha_inicio')
                ->get()
                ->map(function ($row) {
                    return [
                        'id'           => $row->id,
                        'fecha_inicio' => date('d/m/Y', strtotime($row->fecha_inicio)),
                        'fecha_fin'    => date('d/m/Y', strtotime($row->fecha_fin)),
                    ];
                });

            return response()->json($semanas);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function guardarHistorico(Request $request)
    {
        try {
            // 🔹 1. Verificar que el usuario esté autenticado
            if (!$request->user()) {
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // 🔹 2. Validar datos recibidos
            $data = $request->validate([
                'punto_id' => 'required|integer',
                'fecha_inicio' => 'required|string',
                'fecha_fin' => 'required|string',
                'encargado' => 'required|string',
            ]);

            $punto_id = $data['punto_id'];
            $encargado = $data['encargado'];

            // 🔹 3. Convertir fechas a formato Y-m-d
            $fecha_inicio = Carbon::createFromFormat('d/m/Y', $data['fecha_inicio'])->format('Y-m-d');
            $fecha_fin = Carbon::createFromFormat('d/m/Y', $data['fecha_fin'])->format('Y-m-d');

            // 🔹 4. Verificar si ya existe un histórico
            $existe = DB::table('inventario_historico')
                ->where('punto_id', $punto_id)
                ->where('fecha_inicio', $fecha_inicio)
                ->exists();

            if ($existe) {
                return response()->json(['error' => 'Ya existe un histórico para esta semana'], 400);
            }

            // 🔹 5. Obtener datos de la semana
            $result = DB::table('productos as p')
                ->leftJoin('trazabilidadProductos as r', 'p.id', '=', 'r.traIdProducto')
                ->where('r.traPunto', $punto_id)
                ->whereBetween('r.traFechaMovimiento', [$fecha_inicio, $fecha_fin])
                ->select('p.id', 'p.proNombre', 'p.proSeccion', 'r.traFechaMovimiento', 'r.traCantidad')
                ->get();

            $datos = [];
            foreach ($result as $row) {
                if (!isset($datos[$row->id])) {
                    $datos[$row->id] = [
                        'id' => $row->id,
                        'nombre' => $row->proNombre,
                        'seccion_id' => $row->proSeccion,
                        'registros' => []
                    ];
                }

                $datos[$row->id]['registros'][] = [
                    'fecha' => $row->traFechaMovimiento,
                    'cantidad' => (float) $row->traCantidad
                ];
            }

            // 🔹 6. Insertar histórico
            DB::table('inventario_historico')->insert([
                'punto_id' => $punto_id,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'encargado' => $encargado,
                'datos' => json_encode(array_values($datos)),
            ]);

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cargarHistorico($id)
    {
        try {
            if ($id <= 0) {
                throw new \Exception('ID inválido');
            }

            // Obtener el histórico
            $historico = DB::table('inventario_historico')
                ->select('fecha_inicio', 'fecha_fin', 'punto_id', 'datos')
                ->where('id', $id)
                ->first();

            if (!$historico) {
                throw new \Exception('Histórico no encontrado');
            }

            // Obtener todos los productos
            $productos = DB::table('productos')
                ->select('id', 'proNombre', 'proSeccion')
                ->orderBy('proSeccion')
                ->orderBy('proNombre')
                ->get();

            // Decodificar datos históricos
            $datosHistoricos = json_decode($historico->datos, true) ?? [];

            // Crear mapa para búsqueda rápida
            $mapaHistorico = [];
            foreach ($datosHistoricos as $dato) {
                $mapaHistorico[$dato['id']] = $dato;
            }

            // Combinar productos con datos históricos
            $datosCompletos = [];
            foreach ($productos as $producto) {
                $datosCompletos[] = [
                    'id' => $producto->id,
                    'nombre' => $producto->proNombre,
                    'seccion_id' => $producto->proSeccion,
                    'registros' => $mapaHistorico[$producto->id]['registros'] ?? []
                ];
            }

            return response()->json([
                'fecha_inicio' => date('d/m/Y', strtotime($historico->fecha_inicio)),
                'fecha_fin' => date('d/m/Y', strtotime($historico->fecha_fin)),
                'punto_id' => $historico->punto_id,
                'datos' => $datosCompletos
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            set_time_limit(300); // 5 minutos
            ini_set('memory_limit', '512M'); // o más, según necesidad            
            $spreadsheet = new Spreadsheet();

            // 🔹 Puntos
            $puntos = DB::table('puntos')
                ->select('id', 'nombre')
                ->orderBy('nombre')
                ->get();

            // 🔹 Secciones
            $secciones = DB::table('inventario_secciones')
                ->select('id', 'nombre')
                ->orderBy('id')
                ->get();

            $monday = date('Y-m-d', strtotime('monday this week'));
            $sunday = date('Y-m-d', strtotime('sunday this week'));

            foreach ($puntos as $index => $punto) {
                $sheet = $index === 0
                    ? $spreadsheet->getActiveSheet()
                    : $spreadsheet->createSheet();

                $sheet->setTitle($punto->nombre);

                // Títulos
                $sheet->setCellValue('A1', 'PALOTEO SEMANAL - ' . $punto->nombre);
                $sheet->mergeCells('A1:I1');

                $sheet->setCellValue('A2', 'Semana del ' . date('d/m/Y', strtotime($monday)) . ' al ' . date('d/m/Y', strtotime($sunday)));
                $sheet->mergeCells('A2:I2');

                // Encargado
                $encargado = DB::table('inventario_historico')
                    ->where('punto_id', $punto->id)
                    ->whereRaw('? BETWEEN fecha_inicio AND fecha_fin', [$monday])
                    ->orderByDesc('id')
                    ->limit(1)
                    ->value('encargado') ?? 'No asignado';

                $sheet->setCellValue('A3', 'Encargado: ' . $encargado);
                $sheet->mergeCells('A3:I3');

                // Estilos encabezado
                $headerStyle = [
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F5C02E']]
                ];

                $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
                $sheet->getStyle('A2:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $currentRow = 5;

                foreach ($secciones as $seccion) {
                    // Nombre de la sección
                    $sheet->setCellValue('A' . $currentRow, strtoupper($seccion->nombre));
                    $sheet->mergeCells('A' . $currentRow . ':I' . $currentRow);

                    $sectionStyle = [
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DDDDDD']]
                    ];
                    $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($sectionStyle);

                    $currentRow++;

                    // Cabecera de tabla
                    $sheet->fromArray(
                        ['Producto', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo', 'Total'],
                        null,
                        'A' . $currentRow
                    );

                    $tableHeaderStyle = [
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F5C02E']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                    ];
                    $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($tableHeaderStyle);

                    $currentRow++;

                    // Productos y registros de la semana
                    $productos = DB::select("
                        SELECT p.id, p.proNombre as nombre, 
                            COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 2 THEN r.traCantidad END), 0) as lunes,
                            COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 3 THEN r.traCantidad END), 0) as martes,
                            COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 4 THEN r.traCantidad END), 0) as miercoles,
                            COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 5 THEN r.traCantidad END), 0) as jueves,
                            COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 6 THEN r.traCantidad END), 0) as viernes,
                            COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 7 THEN r.traCantidad END), 0) as sabado,
                            COALESCE(MAX(CASE WHEN DAYOFWEEK(r.traFechaMovimiento) = 1 THEN r.traCantidad END), 0) as domingo
                        FROM productos p
                        LEFT JOIN trazabilidadProductos r ON p.id = r.traIdProducto 
                            AND r.traPunto = ? 
                            AND YEARWEEK(r.traFechaMovimiento, 1) = YEARWEEK(CURRENT_DATE, 1)
                        WHERE p.proSeccion = ?
                        GROUP BY p.id, p.proNombre
                        ORDER BY p.proNombre
                    ", [$punto->id, $seccion->id]);

                    $startDataRow = $currentRow;

                    foreach ($productos as $producto) {
                        $sheet->setCellValue('A' . $currentRow, $producto->nombre);
                        $sheet->setCellValue('B' . $currentRow, $producto->lunes);
                        $sheet->setCellValue('C' . $currentRow, $producto->martes);
                        $sheet->setCellValue('D' . $currentRow, $producto->miercoles);
                        $sheet->setCellValue('E' . $currentRow, $producto->jueves);
                        $sheet->setCellValue('F' . $currentRow, $producto->viernes);
                        $sheet->setCellValue('G' . $currentRow, $producto->sabado);
                        $sheet->setCellValue('H' . $currentRow, $producto->domingo);
                        $sheet->setCellValue('I' . $currentRow, '=SUM(B' . $currentRow . ':H' . $currentRow . ')');

                        $currentRow++;
                    }

                    if ($currentRow > $startDataRow) {
                        $dataCellStyle = [
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                        ];
                        $sheet->getStyle('A' . $startDataRow . ':I' . ($currentRow - 1))->applyFromArray($dataCellStyle);
                        $sheet->getStyle('A' . $startDataRow . ':A' . ($currentRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }

                    $currentRow += 2;
                }

                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }

            // 📤 Responder con el archivo Excel como descarga
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Paloteo_Semanal.xlsx';

            return new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            }, 200, [
                "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "Content-Disposition" => "attachment; filename=\"$fileName\"",
                "Cache-Control" => "max-age=0",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // ⚠️ Solo en desarrollo, no en producción
                'trace' => $e->getTraceAsString()
            ], 500);
        }

    }
}
