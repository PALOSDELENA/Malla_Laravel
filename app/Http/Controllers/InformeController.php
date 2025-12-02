<?php

namespace App\Http\Controllers;

use App\Models\CompraInsumo;
use App\Models\ConsumoInsumo;
use App\Models\Encuesta;
use App\Models\FacturasPro;
use App\Models\FacturasSer;
use App\Models\PuntoInfo;
use App\Models\Puntos;
use App\Models\Recetas;
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

        $puntos = PuntoInfo::all();
        
        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();

        foreach ($puntos as $index => $punto) {
            // Consultar los datos
            $insumos = ConsumoInsumo::whereBetween('fecha_insercion', [$fechaInicio, $fechaFin])
                ->where('punto', 'like', "%{$punto->nombre}%")
                ->orderby('insumo')
                ->get();
            
            $sheet = $index === 0
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet();

            $sheet->setTitle($punto->nombre);

            // Título
            $sheet->setCellValue('A1', 'Reporte de Consumo de Insumos - ' . $punto->nombre);
            $sheet->mergeCells('A1:D1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // Encabezados
            $sheet->fromArray(['ID', 'INSUMO', 'CANTIDAD', 'FECHA'], NULL, 'A3');

            // Contenido
            $fila = 4;
            foreach ($insumos as $item) {
                $sheet->setCellValue("A{$fila}", $item->id_consumo);
                $sheet->setCellValue("B{$fila}", $item->insumo);
                $sheet->setCellValue("C{$fila}", $item->cantidad_usada);
                $sheet->setCellValue("D{$fila}", $item->fecha_insercion ? date('d/m/Y H:i:s', strtotime($item->fecha)) : '');
                $fila++;
            }

            // Autoajustar columnas
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Centrar el título
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Centrar y poner en negrita los encabezados
            $sheet->getStyle('A3:D3')->applyFromArray([
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
        }

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
    
    public function exportarFacturasPro(Request $request)
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
        $facturas = FacturasPro::whereBetween('fecha_insercion', [$fechaInicio, $fechaFin])->get();

        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'Reporte de facturas - Proveedor');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Encabezados
        $sheet->fromArray(['ID', 'NIT', 'PROVEEDOR', 'CONCEPTO', 'ID FACTURA', 'Nº FACTURA', 'PUNTO', 'ESTADO PAGO', 'VALOR FACTURA', 'VALOR PAGO', 'ESTADO', 'USUARIO', 'SALDO', 'DETALLE', 'FECHA'], NULL, 'A3');

        // Contenido
        $fila = 4;
        foreach ($facturas as $item) {
            $sheet->setCellValue("A{$fila}", $item->id_factprovee);
            $sheet->setCellValue("B{$fila}", $item->nit ?? '—');
            $sheet->setCellValue("C{$fila}", $item->proveedor);
            $sheet->setCellValue("D{$fila}", $item->concepto);
            $sheet->setCellValue("E{$fila}", $item->idfactura);
            $sheet->setCellValue("F{$fila}", $item->factno);
            $sheet->setCellValue("G{$fila}", $item->punto);
            $sheet->setCellValue("H{$fila}", $item->estadopago);
            $sheet->setCellValue("I{$fila}", $item->valorfact);
            $sheet->setCellValue("J{$fila}", $item->valorpago);
            $sheet->setCellValue("K{$fila}", $item->estado);
            $sheet->setCellValue("L{$fila}", $item->usuario);
            $sheet->setCellValue("M{$fila}", $item->saldo);
            $sheet->setCellValue("N{$fila}", $item->detalle);
            $sheet->setCellValue("O{$fila}", $item->fecha_insercion ? date('d/m/Y H:i:s', strtotime($item->fecha)) : '');
            $fila++;
        }

        // Autoajustar columnas
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Centrar el título
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Centrar y poner en negrita los encabezados
        $sheet->getStyle('A3:O3')->applyFromArray([
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
        $fileName = "Reporte_Facturas_Proveedor_{$fechaInicio}_a_{$fechaFin}.xlsx";

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
    
    public function exportarFacturasSer(Request $request)
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
        $facturas = FacturasSer::whereBetween('fecha_insercion', [$fechaInicio, $fechaFin])->get();

        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'Reporte de facturas - Servicios');
        $sheet->mergeCells('A1:Y1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Encabezados
        $sheet->fromArray(['ID', 'PUNTO', 'ID SERVICIO', 'FACTURA ELEC.', 'TIPO FACTURA', 'VALOR-A', 'DESCUENTO', 'VALOR-B', 'PROPINA', 'IMPUESTO', 'MESERO', 'CAJERO', 'MEDIO DE PAGO', 'TIPO', 'INGRESO', 'SALIDA', 'MIXTOM_1', 'MIXTOM_2', 'MIXTOV_1', 'MIXTOV_2', 'PERSONAS MESA', 'FUENTE', 'DIAN', 'FECHA'], NULL, 'A3');

        // Contenido
        $fila = 4;
        foreach ($facturas as $item) {
            $sheet->setCellValue("A{$fila}", $item->id_ventas);
            $sheet->setCellValue("B{$fila}", $item->punto ?? '—');
            $sheet->setCellValue("C{$fila}", $item->idservicio);
            $sheet->setCellValue("D{$fila}", $item->fact_elec);
            $sheet->setCellValue("E{$fila}", $item->tipofact);
            $sheet->setCellValue("F{$fila}", $item->valora);
            $sheet->setCellValue("G{$fila}", $item->descuento);
            $sheet->setCellValue("H{$fila}", $item->valorb);
            $sheet->setCellValue("I{$fila}", $item->propina);
            $sheet->setCellValue("J{$fila}", $item->impuesto);
            $sheet->setCellValue("K{$fila}", $item->mesero);
            $sheet->setCellValue("L{$fila}", $item->cajero);
            $sheet->setCellValue("M{$fila}", $item->mediopago);
            $sheet->setCellValue("N{$fila}", $item->tipo);
            $sheet->setCellValue("O{$fila}", $item->ingreso ? date('d/m/Y H:i:s', strtotime($item->fecha)) : '');
            $sheet->setCellValue("P{$fila}", $item->salida ? date('d/m/Y H:i:s', strtotime($item->fecha)) : '');
            $sheet->setCellValue("Q{$fila}", $item->mixtom1);
            $sheet->setCellValue("R{$fila}", $item->mixtom2);
            $sheet->setCellValue("S{$fila}", $item->mixtov1);
            $sheet->setCellValue("T{$fila}", $item->mixtov2);
            $sheet->setCellValue("U{$fila}", $item->personasmesa);
            $sheet->setCellValue("V{$fila}", $item->fuente);
            $sheet->setCellValue("W{$fila}", $item->dian);
            $sheet->setCellValue("X{$fila}", $item->fecha_insercion ? date('d/m/Y H:i:s', strtotime($item->fecha)) : '');
            $fila++;
        }

        // Autoajustar columnas
        foreach (range('A', 'X') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Centrar el título
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Centrar y poner en negrita los encabezados
        $sheet->getStyle('A3:X3')->applyFromArray([
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
        $fileName = "Reporte_Facturas_Servicios_{$fechaInicio}_a_{$fechaFin}.xlsx";

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
    
    public function exportarRecetas(Request $request)
    {
        $punto = $request->input('punto');

        // Validar fechas
        if (!$punto) {
            return back()->with('error', 'Debe seleccionar un punto.');
        }

        // Consultar los datos
        $puntos = Recetas::where('punto', $punto)->get();

        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'Reporte de Recetas');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Encabezados
        $sheet->fromArray(['ID', 'PRODUCTO', 'INSUMO', 'CANTIDAD.', 'PUNTO'], NULL, 'A3');

        // Contenido
        $fila = 4;
        foreach ($puntos as $item) {
            $sheet->setCellValue("A{$fila}", $item->id_recetas);
            $sheet->setCellValue("B{$fila}", $item->producto ?? '—');
            $sheet->setCellValue("C{$fila}", $item->insumo);
            $sheet->setCellValue("D{$fila}", $item->cantidad);
            $sheet->setCellValue("E{$fila}", $item->punto);
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
        $fileName = "Reporte_Recetas_{$punto}.xlsx";

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

    public function exportarEncuesta(Request $request)
    {
        $punto = $request->input('punto');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Validar fechas
        if (!$punto || !$fechaInicio || !$fechaFin) {
            return back()->with('error', 'Debe seleccionar un punto y fechas.');
        }

        // Agregar rango de horas
        $fechaInicio = $fechaInicio . ' 00:00:00';
        $fechaFin = $fechaFin . ' 23:59:59';    
        
        // Asignar ID del punto según el nombre
        switch ($punto) {
            case 'PUENTE ARANDA':    $punto_id = 1; break;
            case 'QUINTA PAREDES':   $punto_id = 2; break;
            case 'MALL':             $punto_id = 3; break;
            case 'CENTRO':           $punto_id = 4; break;
            case 'JIMENEZ':          $punto_id = 5; break;
            case 'MULTI':            $punto_id = 6; break;
            case 'SALITRE PLAZA':    $punto_id = 7; break;
            case 'CAFAM FLORESTA':   $punto_id = 8; break;
            case 'NUESTRO BOGOTA':   $punto_id = 9; break;
            case 'FONTIBON':         $punto_id = 10; break;
            case 'HAYUELOS':         $punto_id = 11; break;
            default:                 $punto_id = null; break;
        }

        // Consultar los datos filtrando por punto y rango de fechas
        $encuestas = Encuesta::where('punto_id', $punto_id)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->get();

        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'Reporte de Encuesta');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Encabezados
        $sheet->fromArray(['PUNTO', 'TIEMPO DE ESPERA', 'SERVICIO', 'TEMPERATURA', 'EXPERIENCIA', 'FECHA'], NULL, 'A3');

        // Contenido
        $fila = 4;
        foreach ($encuestas as $item) {
            $sheet->setCellValue("A{$fila}", $punto);
            $sheet->setCellValue("B{$fila}", $item->tiempo_espera ?? '—');
            $sheet->setCellValue("C{$fila}", $item->servicio);
            $sheet->setCellValue("D{$fila}", $item->temperatura);
            $sheet->setCellValue("E{$fila}", $item->experiencia);
            $sheet->setCellValue("F{$fila}", $item->created_at ? date('d/m/Y H:i:s', strtotime($item->created_at)) : '');
            $fila++;
        }

        // Autoajustar columnas
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Centrar el título
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Centrar y poner en negrita los encabezados
        $sheet->getStyle('A3:F3')->applyFromArray([
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
        $fileName = "Reporte_Recetas_{$punto}.xlsx";

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
