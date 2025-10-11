<?php

namespace App\Http\Controllers;

use App\Models\CompraInsumo;
use App\Models\ConsumoInsumo;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
class InformeController extends Controller
{
    public function index()
    {
        return view('admin_informes.index');
    }

    public function obtenerConsumo()
    {
        try {
            // Consultar todos los registros de la tabla
            $data = ConsumoInsumo::limit(10)->get(); // puedes quitar limit() si quieres todo

            return response()->json([
                'status' => 'success',
                'total' => $data->count(),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportarCompraInsumos(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Validar fechas
        if (!$fechaInicio || !$fechaFin) {
            return back()->with('error', 'Debe seleccionar ambas fechas.');
        }

        // Agregar rango de horas
        $fechaInicio = $fechaInicio . ' 00:00:00';
        $fechaFin = $fechaFin . ' 23:59:59';        

        // Consultar los datos
        $insumos = CompraInsumo::whereBetween('fecha_insercion', [$fechaInicio, $fechaFin])->get();

        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'Reporte de Compras de Insumos');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Encabezados
        $sheet->fromArray(['ID', 'NIT', 'PROVEEDOR', 'PUNTO', 'INSUMO', 'MEDIDA', 'CANTIDAD', 'PRECIO', 'IMPUESTO', 'CANTIDAD ANTERIOR', 'PRECIO ANTERIOR', 'FECHA'], NULL, 'A3');

        // Contenido
        $fila = 4;
        foreach ($insumos as $item) {
            $sheet->setCellValue("A{$fila}", $item->id_compra);
            $sheet->setCellValue("B{$fila}", $item->nit ?? '—');
            $sheet->setCellValue("C{$fila}", $item->proveedor);
            $sheet->setCellValue("D{$fila}", $item->punto);
            $sheet->setCellValue("E{$fila}", $item->insumo ?? '—');
            $sheet->setCellValue("F{$fila}", $item->medida ?? '—');
            $sheet->setCellValue("G{$fila}", $item->cantidad ?? 0);
            $sheet->setCellValue("H{$fila}", $item->precio ?? 0);
            $sheet->setCellValue("I{$fila}", $item->impuesto ?? 0);
            $sheet->setCellValue("J{$fila}", $item->cantidad_anterior ?? 0);
            $sheet->setCellValue("K{$fila}", $item->precio_anterior ?? 0);
            $sheet->setCellValue("L{$fila}", $item->fecha ? date('d/m/Y H:i:s', strtotime($item->fecha)) : '');
            // Formato de moneda para las columnas H y K
            $sheet->getStyle('H4:H' . ($fila - 1))
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0.00');

            $sheet->getStyle('K4:K' . ($fila - 1))
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0.00');
            $fila++;
        }

        // Autoajustar columnas
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Centrar el título
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Centrar y poner en negrita los encabezados
        $sheet->getStyle('A3:L3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Crear nombre del archivo
        $fileName = "Reporte_Compras_Insumos_{$fechaInicio}_a_{$fechaFin}.xlsx";

        // Descargar
        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"$fileName\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }   
  
    public function exportarConsumoInsumos(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Validar fechas
        if (!$fechaInicio || !$fechaFin) {
            return back()->with('error', 'Debe seleccionar ambas fechas.');
        }

        // Agregar rango de horas
        $fechaInicio = $fechaInicio . ' 00:00:00';
        $fechaFin = $fechaFin . ' 23:59:59';        

        // Consultar los datos
        $insumos = ConsumoInsumo::whereBetween('fecha_insercion', [$fechaInicio, $fechaFin])->get();

        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'Reporte de Consumo de Insumos');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Encabezados
        $sheet->fromArray(['ID', 'PUNTO', 'INSUMO', 'CANTIDAD', 'FECHA'], NULL, 'A3');

        // Contenido
        $fila = 4;
        foreach ($insumos as $item) {
            $sheet->setCellValue("A{$fila}", $item->id_consumo);
            $sheet->setCellValue("B{$fila}", $item->punto ?? '—');
            $sheet->setCellValue("C{$fila}", $item->insumo);
            $sheet->setCellValue("D{$fila}", $item->cantidad_usada);
            $sheet->setCellValue("E{$fila}", $item->fecha_insercion ? date('d/m/Y H:i:s', strtotime($item->fecha)) : '');
            $fila++;
        }

        // Autoajustar columnas
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Centrar el título
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Centrar y poner en negrita los encabezados
        $sheet->getStyle('A3:E3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Crear nombre del archivo
        $fileName = "Reporte_Consumo_Insumos_{$fechaInicio}_a_{$fechaFin}.xlsx";

        // Descargar
        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"$fileName\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }   
    
}
