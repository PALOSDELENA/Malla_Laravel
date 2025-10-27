<?php

namespace App\Http\Controllers;

use App\Models\NovedadPaloteo;
use App\Models\Productos;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // O usa Imagick si lo prefieres
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class NovedadPaloteoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $punto = $user->usu_punto;
        $insumos = Productos::all();

        // Determinar si el usuario es administrativo o planta
        $esAdminOPlanta = in_array($punto, [3, 17]);

        // Construir la consulta
        $query = NovedadPaloteo::query();

        if (!$esAdminOPlanta) {
            $query->where('id_punto', $punto);
        }

        // Aplicar otros filtros
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_novedad', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $novedades = $query->paginate(10);

        foreach ($novedades as $nov) {
            if (is_string($nov->imagenes)) {
                $decoded = json_decode($nov->imagenes, true);

                // Si aún sigue siendo un string (doble encode), lo decodificamos otra vez
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }

                $nov->imagenes = $decoded;
            }
        }

        return view('admin_novedades.index', compact('novedades', 'insumos', 'punto'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'comentario_operario' => 'required|string',
            'fecha_novedad' => 'required|date',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // hasta 5MB permitidos
        ], [
            'id_producto.required' => 'Debes seleccionar un producto.',
            'id_producto.exists' => 'El producto seleccionado no existe.',
            'comentario_operario.required' => 'El comentario del operario es obligatorio.',
            'fecha_novedad.required' => 'Debes indicar la fecha de la novedad.',
            'imagenes.*.image' => 'Cada archivo debe ser una imagen.',
            'imagenes.*.mimes' => 'Las imágenes deben ser de tipo jpeg, png, jpg o gif.',
            'imagenes.*.max' => 'Cada imagen no puede superar los 5MB.',
        ]);

        $imagenesGuardadas = [];
        $manager = new ImageManager(new Driver()); // Crea el manejador (usa GD por defecto)

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                // Calcular tamaño en MB
                $sizeMB = $imagen->getSize() / 1024 / 1024;

                // Determinar calidad según tamaño
                $quality = $sizeMB > 4 ? 60 : ($sizeMB > 2 ? 75 : 90);

                // Leer la imagen con Intervention 3
                $img = $manager->read($imagen->getRealPath());

                // Codificar (comprimir)
                $encoded = $img->encodeByExtension(
                    $imagen->getClientOriginalExtension(),
                    quality: $quality
                );

                // Generar nombre único
                $nombreArchivo = uniqid() . '.' . $imagen->getClientOriginalExtension();

                // Guardar en storage/app/public/novedades_paloteo
                Storage::disk('public')->put("novedades_paloteo/{$nombreArchivo}", $encoded->toString());

                // Registrar la ruta
                $imagenesGuardadas[] = "novedades_paloteo/{$nombreArchivo}";
            }
        }

        $user = Auth::user();

        NovedadPaloteo::create([
            'id_usuario' => $user->num_doc,
            'id_punto' => $user->usu_punto,
            'id_producto' => $request->id_producto,
            'comentario_operario' => $request->comentario_operario,
            'fecha_novedad' => $request->fecha_novedad,
            'imagenes' => json_encode($imagenesGuardadas),
        ]);

        return redirect()->route('novedad.index')->with('success', 'Novedad registrada exitosamente.');
    }

    public function update(Request $request, $id)
    {
        // Validar solo el comentario_admin
        $request->validate([
            'comentario_admin' => 'required|string',
        ]);

        // Buscar la novedad por ID
        $novedad = NovedadPaloteo::findOrFail($id);

        // Actualizar el comentario del admin
        $novedad->comentario_admin = $request->comentario_admin;
        $novedad->estado = 'revisado';
        $novedad->save();

        // Redireccionar de vuelta con mensaje
        return redirect()->route('novedad.index')
                        ->with('success', 'Comentario del admin actualizado correctamente.');
    }

    public function exportarExcel(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $novedades = NovedadPaloteo::with(['producto', 'punto'])
            ->whereBetween('fecha_novedad', [$fechaInicio, $fechaFin])
            ->get();

        if ($novedades->isEmpty()) {
            return back()->with('warning', 'No hay novedades en el rango seleccionado.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Novedades');

        // Títulos
        $sheet->setCellValue('A1', 'Reporte de Novedades');
        $sheet->mergeCells('A1:G1');
        
        // Aplicar estilo al título
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14, // Tamaño de letra más grande
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Ajustar altura de fila para mejor apariencia
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Encabezados
        $headers = ['Insumo', 'Comentario Operario', 'Comentario Admin', 'Fecha Novedad', 'Estado', 'Punto', 'Imágenes'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '2', $header);
            $sheet->getStyle($col . '2')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setWidth(25);

            // Centrar el texto horizontal y verticalmente
            $sheet->getStyle($col . '2')->getAlignment()->setHorizontal('center');
            $sheet->getStyle($col . '2')->getAlignment()->setVertical('center');
            
            $col++;
        }

        $fila = 3;
        foreach ($novedades as $nov) {
            $sheet->setCellValue("A{$fila}", $nov->producto->proNombre ?? '—');
            $sheet->setCellValue("B{$fila}", $nov->comentario_operario ?? '—');
            $sheet->setCellValue("C{$fila}", $nov->comentario_admin ?? '—');
            $sheet->setCellValue("D{$fila}", \Carbon\Carbon::parse($nov->fecha_novedad)->format('d-m-Y'));
            $sheet->setCellValue("E{$fila}", $nov->estado ?? '—');
            $sheet->setCellValue("F{$fila}", $nov->punto->nombre ?? '—');

            // Imágenes
            $imagenes = [];

            if (!empty($nov->imagenes)) {
                $data = $nov->imagenes;

                // Si ya viene como array, se usa tal cual
                if (is_array($data)) {
                    $imagenes = $data;
                } else {
                    // Primera decodificación
                    $decoded = json_decode($data, true);

                    // Si el resultado sigue siendo string, se decodifica otra vez
                    if (is_string($decoded)) {
                        $decoded = json_decode($decoded, true);
                    }

                    if (is_array($decoded)) {
                        $imagenes = $decoded;
                    }
                }
            }

            if (!empty($imagenes)) {
                $columnaImagen = 'G';
                $offsetX = 0;
                foreach ($imagenes as $img) {
                    $ruta = Storage::disk('public')->path($img);

                    if (file_exists($ruta)) {
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setPath($ruta);
                        $drawing->setCoordinates("{$columnaImagen}{$fila}");
                        $drawing->setOffsetX($offsetX);
                        $drawing->setHeight(50);
                        $drawing->setWorksheet($sheet);
                        $offsetX += 55;
                    }
                }
                $sheet->getRowDimension($fila)->setRowHeight(55);
            } else {
                $sheet->setCellValue("G{$fila}", '—');
            }

            $fila++;
        }

        $filename = 'Novedades_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }
}
