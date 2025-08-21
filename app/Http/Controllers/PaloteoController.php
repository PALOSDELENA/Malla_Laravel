<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Puntos;
use App\Models\Seccion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

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

    public function obtenerGerente($punto)
    {
        $gerente = User::where('usu_punto', $punto)
            ->where('usu_cargo', 4)
            ->first();

        if ($gerente) {
            return response()->json([
                'success' => true,
                'nombre_encargado' => $gerente->usu_nombre
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
                        'traResponsable' => 4869681,
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
            ]);

            $punto_id = $data['punto_id'];

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
                'datos' => json_encode(array_values($datos)),
            ]);

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }}
