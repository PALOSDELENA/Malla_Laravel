<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Puntos;
use App\Models\Cotizacion;
use App\Models\Cliente;
use App\Models\ItemExtras;
use App\Models\CotizacionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotizacion::with(['cliente', 'items', 'punto']);
        
        // Apply filters if present
        if ($request->filled('filter_cliente')) {
            $query->whereHas('cliente', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->filter_cliente . '%');
            });
        }
        
        if ($request->filled('filter_sede')) {
            $query->where(function($q) use ($request) {
                $q->where('sede', 'like', '%' . $request->filter_sede . '%')
                  ->orWhereHas('punto', function($subQ) use ($request) {
                      $subQ->where('nombre', 'like', '%' . $request->filter_sede . '%');
                  });
            });
        }
        
        if ($request->filled('filter_fecha')) {
            $query->whereDate('fecha', $request->filter_fecha);
        }
        
        $cotizaciones = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->except('page'));
        
        return view('cotizaciones.index', compact('cotizaciones'));
    }

    /**
     * Display the specified cotizacion with related data.
     */
    public function show($id)
    {
        $cot = Cotizacion::with(['cliente', 'items.producto', 'punto'])->findOrFail($id);
        return view('cotizaciones.show', compact('cot'));
    }

    public function create()
    {
        $extras = ItemExtras::all();
        $clientes = Cliente::orderBy('created_at', 'desc')->get();
        $productos = Productos::whereIn('proTipo', ['Carta-E', 'Carta-F', 'Carta-P', 'Carta-B'])->get();
        $sedes = Puntos::whereNotIn('nombre', ['Planta', 'Administrativo', 'Cocina', 'Parrilla'])->get();
        return view('cotizaciones.create', compact('extras', 'clientes', 'productos', 'sedes'));
    }

    /**
     * Export a single cotizacion to Excel (.xlsx)
     */
    /**
     * Export a single cotizacion to Excel (.xlsx)
     */
    public function exportExcel($id)
    {
        $cot = Cotizacion::with(['cliente', 'items.producto', 'punto', 'itemExtras'])->findOrFail($id);

        $spreadsheet = $this->generateCotizacionSpreadsheet($cot, false);

        $writer = new Xlsx($spreadsheet);
        $filename = 'cotizacion_' . $cot->id . '_' . $cot->cliente->nombre . '.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return $response;

    }

    /**
     * Export a single cotizacion to PDF
     */
    public function exportPdf($id)
    {
        $cot = Cotizacion::with(['cliente', 'items.producto', 'punto', 'itemExtras'])->findOrFail($id);

        $spreadsheet = $this->generateCotizacionSpreadsheet($cot, true);

        // Configure PDF writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf($spreadsheet);
        
        // Set paper size, orientation and margins
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        
        // Set page margins to prevent border cutting
        $spreadsheet->getActiveSheet()->getPageMargins()
            ->setTop(0.5)
            ->setRight(0.5)
            ->setLeft(0.5)
            ->setBottom(0.5);

        $filename = 'cotizacion_' . $cot->id . '_' . $cot->cliente->nombre .'.pdf';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return $response;
    }

    /**
     * Generate spreadsheet for a cotizacion (shared logic for Excel and PDF export)
     * @param bool $forPdf - true if generating for PDF export, false for Excel
     */
    private function generateCotizacionSpreadsheet($cot, $forPdf = false)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cotizacion ' . $cot->id);

        // Merge cells A1:F14 for header/logo space
        $sheet->mergeCells('A1:F14');

        // Insert logo image in A1:F14
        $logoPath = public_path('img/logo_excel.jpg');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo Palos de Leña');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            
            // Different offsets for PDF vs Excel
            if ($forPdf) {
                $drawing->setOffsetX(100);
                $drawing->setOffsetY(6);
            } else {
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(10);
            }
            
            // Set the bottom-right anchor to F14
            $drawing->setCoordinates2('F14');
            $drawing->setOffsetX2(-10);
            $drawing->setOffsetY2(-10);
            // Adjust height to fit within the merged cells (14 rows)
            $drawing->setHeight(180);
            $drawing->setResizeProportional(true);
            $drawing->setWorksheet($sheet);
        }

        // Start placing content according to requested layout. We'll map rows exactly.
        // We'll use row numbers from the user's spec (15.. etc). Start by leaving rows 1..14 empty.
        $row = 15;

        // A15-D15 merged = Client name (label bold, value normal)
        $sheet->mergeCells('A15:D15');
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $boldPart = $richText->createTextRun('Nombre: ');
        $boldPart->getFont()->setBold(true);
        $normalPart = $richText->createTextRun($cot->cliente->nombre ?? '');
        $sheet->setCellValue('A15', $richText);

        // E15-F15 merged = Sede (label bold, value normal)
        $sheet->mergeCells('E15:F15');
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $boldPart = $richText->createTextRun('Sede: ');
        $boldPart->getFont()->setBold(true);
        $normalPart = $richText->createTextRun($cot->punto->nombre ?? '');
        $sheet->setCellValue('E15', $richText);

        // A16-D16 = Motivo (label bold, value normal)
        $sheet->mergeCells('A16:D16');
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $boldPart = $richText->createTextRun('Motivo: ');
        $boldPart->getFont()->setBold(true);
        $normalPart = $richText->createTextRun($cot->motivo ?? '');
        $sheet->setCellValue('A16', $richText);

        // E16-F16 = Hora (label bold, value normal)
        $sheet->mergeCells('E16:F16');
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $boldPart = $richText->createTextRun('Hora: ');
        $boldPart->getFont()->setBold(true);
        $normalPart = $richText->createTextRun($cot->hora ?? '');
        $sheet->setCellValue('E16', $richText);

        // A17-D17 = Celular (label bold, value normal)
        $sheet->mergeCells('A17:D17');
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $boldPart = $richText->createTextRun('Celular: ');
        $boldPart->getFont()->setBold(true);
        $normalPart = $richText->createTextRun($cot->cliente->celular ?? '');
        $sheet->setCellValue('A17', $richText);

        // E17-F17 = Fecha (label bold, value normal)
        $sheet->mergeCells('E17:F17');
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $boldPart = $richText->createTextRun('Fecha: ');
        $boldPart->getFont()->setBold(true);
        $normalPart = $richText->createTextRun($cot->fecha ? (is_object($cot->fecha) ? $cot->fecha->format('Y-m-d') : \Carbon\Carbon::parse($cot->fecha)->format('Y-m-d')) : '');
        $sheet->setCellValue('E17', $richText);

        // A18-D18 = Correo (label bold, value normal)
        $sheet->mergeCells('A18:D18');
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $boldPart = $richText->createTextRun('Correo: ');
        $boldPart->getFont()->setBold(true);
        $normalPart = $richText->createTextRun($cot->cliente->correo ?? '');
        $sheet->setCellValue('A18', $richText);

        // E18-F18 = Numero de personas (label bold, value normal)
        $sheet->mergeCells('E18:F18');
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $boldPart = $richText->createTextRun('Numero de personas: ');
        $boldPart->getFont()->setBold(true);
        $normalPart = $richText->createTextRun($cot->numero_personas ?? '');
        $sheet->setCellValue('E18', $richText);

        // A19-F20 merged (two rows) with text 'OPCIONES'
        $sheet->mergeCells('A19:F20');
        $sheet->setCellValue('A19', 'OPCIONES');
        $sheet->getStyle('A19')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A19')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        // Apply background color #652726 and white font to merged OPCIONES cell
        $sheet->getStyle('A19:F20')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('652726');
        $sheet->getStyle('A19:F20')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A19:F20')->getFont()->setBold(true);

        // A21-C21 empty
        $sheet->mergeCells('A21:C21');
        $sheet->setCellValue('A21', '');

        // D21: Cantidad, E21: Precio por Unidad, F21: Total
        $sheet->setCellValue('D21', 'Cantidad');
        $sheet->setCellValue('E21', 'Precio por Unidad');
        $sheet->setCellValue('F21', 'Total');

        // Starting products at row 22, organized by categories
        $startProductsRow = 22;
        $current = $startProductsRow;

        // Define categories and their corresponding proTipo values
        $categories = [
            'ENTRADAS' => 'Carta-E',
            'POSTRES' => 'Carta-P',
            'PLATOS FUERTES' => 'Carta-F',
            'BEBIDAS' => 'Carta-B',
        ];

        foreach ($categories as $categoryName => $categoryType) {
            // Filter items by category
            $categoryItems = $cot->items->filter(function($item) use ($categoryType) {
                return isset($item->producto->proTipo) && $item->producto->proTipo === $categoryType;
            });

            // Only show category if it has items
            if ($categoryItems->count() > 0) {
                // Category header row
                $sheet->mergeCells('A' . $current . ':C' . $current);
                $sheet->setCellValue('A' . $current, $categoryName);
                $sheet->mergeCells('D' . $current . ':F' . $current);
                $sheet->setCellValue('D' . $current, '');
                // Apply light gray background to category header
                $sheet->getStyle('A' . $current . ':F' . $current)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D3D3D3');
                $sheet->getStyle('A' . $current . ':F' . $current)->getFont()->setBold(true);
                $current++;

                // Print items in this category
                foreach ($categoryItems as $item) {
                    $sheet->mergeCells('A' . $current . ':C' . $current);
                    $sheet->setCellValue('A' . $current, $item->producto->proNombre ?? '');
                    $sheet->setCellValue('D' . $current, (float) $item->cantidad);
                    $sheet->setCellValue('E' . $current, (float) $item->producto_precio);
                    $sheet->setCellValue('F' . $current, (float) $item->total_item);
                    $current++;
                }
            }
        }

        // Items Extras (OTROS) - si existen
        if ($cot->itemExtras && $cot->itemExtras->count() > 0) {
            // Category header row para OTROS
            $sheet->mergeCells('A' . $current . ':C' . $current);
            $sheet->setCellValue('A' . $current, 'OTROS');
            $sheet->mergeCells('D' . $current . ':F' . $current);
            $sheet->setCellValue('D' . $current, '');
            // Apply light gray background to category header
            $sheet->getStyle('A' . $current . ':F' . $current)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D3D3D3');
            $sheet->getStyle('A' . $current . ':F' . $current)->getFont()->setBold(true);
            $current++;

            // Print items extras
            foreach ($cot->itemExtras as $extra) {
                $sheet->mergeCells('A' . $current . ':C' . $current);
                $nombreExtra = $extra->pivot->nombre;
                $cantidad = $extra->pivot->cantidad ?? 1;
                $valorUnitario = (float) $extra->pivot->valor;
                $totalLinea = $cantidad * $valorUnitario;
                
                // Si no suma al total, agregar indicador
                if (!$extra->pivot->suma_al_total) {
                    $nombreExtra .= ' (No incluido)';
                }
                
                $sheet->setCellValue('A' . $current, $nombreExtra);
                $sheet->setCellValue('D' . $current, $cantidad); // Cantidad
                $sheet->setCellValue('E' . $current, $valorUnitario); // Valor Unitario
                $sheet->setCellValue('F' . $current, $totalLinea); // Total
                
                // Si no suma al total, aplicar estilo diferente (texto gris)
                if (!$extra->pivot->suma_al_total) {
                    $sheet->getStyle('A' . $current . ':F' . $current)->getFont()->getColor()->setRGB('808080');
                    $sheet->getStyle('A' . $current . ':F' . $current)->getFont()->setItalic(true);
                }
                
                $current++;
            }
        }

        $lastProductRow = max($startProductsRow, $current - 1);

        // Calcular el total manualmente: suma de productos + items extras que suman al total
        $totalProductos = 0;
        foreach ($cot->items as $item) {
            $totalProductos += (float) $item->total_item;
        }
        
        $totalExtrasIncluidos = 0;
        if ($cot->itemExtras && $cot->itemExtras->count() > 0) {
            foreach ($cot->itemExtras as $extra) {
                if ($extra->pivot->suma_al_total) {
                    $cant = $extra->pivot->cantidad ?? 1;
                    $val = (float) $extra->pivot->valor;
                    $totalExtrasIncluidos += ($cant * $val);
                }
            }
        }
        
        $totalAlimentosYBebidas = $totalProductos + $totalExtrasIncluidos;

        // After products, next row: merge A-E and put 'TOTAL ALIMENTOS Y BEBIDAS, OTROS' and in F place sum of totals
        $sumRow = $current;
        $sheet->mergeCells('A' . $sumRow . ':E' . $sumRow);
        $sheet->setCellValue('A' . $sumRow, 'TOTAL ALIMENTOS Y BEBIDAS, OTROS');
        $sheet->setCellValue('F' . $sumRow, $totalAlimentosYBebidas);
        // Style the TOTAL row with the same color as OPCIONES (#652726) and white text for full A-F
        $sheet->getStyle('A' . $sumRow . ':F' . $sumRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('652726');
        $sheet->getStyle('A' . $sumRow . ':F' . $sumRow)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $sumRow . ':F' . $sumRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $sumRow . ':F' . $sumRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $r = $sumRow + 1;

        // Total de items extras (solo los que suman al total)
        if ($cot->itemExtras && $cot->itemExtras->count() > 0) {
            $totalExtras = 0;
            foreach ($cot->itemExtras as $extra) {
                if ($extra->pivot->suma_al_total) {
                    $cant = $extra->pivot->cantidad ?? 1;
                    $val = (float) $extra->pivot->valor;
                    $totalExtras += ($cant * $val);
                }
            }
            
            if ($totalExtras > 0) {
                $sheet->mergeCells('A' . $r . ':E' . $r);
                $sheet->setCellValue('A' . $r, 'TOTAL ITEMS EXTRAS');
                $sheet->setCellValue('F' . $r, $totalExtras);
                // Style the row
                $sheet->getStyle('A' . $r . ':F' . $r)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('652726');
                $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
                $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
                $sheet->getStyle('A' . $r . ':F' . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $r++;
            }
        }

        // Next row: merge A-E and put 'SUBTOTAL' and in F put subtotal from DB
        $subtotalRow = $r;
        $sheet->mergeCells('A' . $subtotalRow . ':E' . $subtotalRow);
        $sheet->setCellValue('A' . $subtotalRow, 'SUBTOTAL');
        $sheet->setCellValue('F' . $subtotalRow, (float) $cot->subtotal);
        // style subtotal full row A-F
        $sheet->getStyle('A' . $subtotalRow . ':F' . $subtotalRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('652726');
        $sheet->getStyle('A' . $subtotalRow . ':F' . $subtotalRow)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $subtotalRow . ':F' . $subtotalRow)->getFont()->setBold(true);

        // Apply bold to headers and totals
        $sheet->getStyle('A19')->getFont()->setBold(true);
        $sheet->getStyle('D21:F21')->getFont()->setBold(true);

        // Now add the additional rows after SUBTOTAL in the requested order.
        $r = $subtotalRow + 1;

        // ipoconsumo (show even if zero per spec)
        $sheet->mergeCells('A' . $r . ':E' . $r);
        $sheet->setCellValue('A' . $r, 'IPOCONSUMO');
        $sheet->setCellValue('F' . $r, (float) $cot->ipoconsumo);
        // style row
        $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
        $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
        $r++;

        // retefuente (only if > 0)
        if (!empty($cot->retefuente) && (float)$cot->retefuente > 0) {
            $sheet->mergeCells('A' . $r . ':E' . $r);
            $sheet->setCellValue('A' . $r, 'RETEFUENTE');
            $sheet->setCellValue('F' . $r, (float) $cot->retefuente);
            // style row
            $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
            $r++;
        }

        // reteica (only if > 0)
        if (!empty($cot->reteica) && (float)$cot->reteica > 0) {
            $sheet->mergeCells('A' . $r . ':E' . $r);
            $sheet->setCellValue('A' . $r, 'RETEICA');
            $sheet->setCellValue('F' . $r, (float) $cot->reteica);
            // style row
            $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
            $r++;
        }

        // propina (only if > 0)
        if (!empty($cot->propina) && (float)$cot->propina > 0) {
            $sheet->mergeCells('A' . $r . ':E' . $r);
            $sheet->setCellValue('A' . $r, 'SERVICIO');
            $sheet->setCellValue('F' . $r, (float) $cot->propina);
            // style row
            $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
            $r++;
        }

        // descuento (%) if present
        $descuentoPctRow = null;
        if (!empty($cot->descuento_pct) && (float)$cot->descuento_pct > 0) {
            $descuentoPctRow = $r;
            $sheet->mergeCells('A' . $r . ':E' . $r);
            $sheet->setCellValue('A' . $r, 'DESCUENTO (%)');
            $sheet->setCellValue('F' . $r, (float) $cot->descuento_pct);
            // style row
            $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
            $r++;
        }

        // valor del descuento (monto) if present
        if (!empty($cot->descuento_monto) && (float)$cot->descuento_monto > 0) {
            $sheet->mergeCells('A' . $r . ':E' . $r);
            $sheet->setCellValue('A' . $r, 'VALOR DESCUENTO');
            $sheet->setCellValue('F' . $r, (float) $cot->descuento_monto);
            // style row
            $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
            $r++;
        }

        // anticipo
        $sheet->mergeCells('A' . $r . ':E' . $r);
        $sheet->setCellValue('A' . $r, 'ANTICIPO');
        $sheet->setCellValue('F' . $r, (float) $cot->anticipo);
        // style row
        $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
        $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
        $r++;

        // valor restante (saldo_pendiente)
        $sheet->mergeCells('A' . $r . ':E' . $r);
        $sheet->setCellValue('A' . $r, 'VALOR RESTANTE');
        $sheet->setCellValue('F' . $r, (float) $cot->saldo_pendiente);
        // style row
        $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
        $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
        $r++;

        // valor total (total_final)
        $sheet->mergeCells('A' . $r . ':E' . $r);
        $sheet->setCellValue('A' . $r, 'VALOR TOTAL');
        $sheet->setCellValue('F' . $r, (float) $cot->total_final);
        // style row
        $sheet->getStyle('A' . $r . ':F' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('652726');
        $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $r . ':F' . $r)->getFont()->setBold(true);
        $lastTotalRow = $r; // Save the last total row before terms
        $r++;

        // Terms and conditions section
        $terms = [
            'El impuesto del 8% de impoconsumo ya esta incluido en el valor total de los platos.',
            'TODOS NUESTROS VALORES ESTAN SUJETOS A CAMBIOS',
            'NO MANEJAMOS EXCLUSIVIDAD EN NINGUNA SEDE',
            'Si requiere de facturación electronica, por favor comunicarse al correo facturacionelectronica@palosdelena.com',
            'Para garantizar el espacio , solicitamos un anticipo  del 50% del total de la cotización Y EL OTRO 50% AL FINALIZAR EL EVENTO,  los consumos adicionales que se presenten serán cancelados durante o al finalizar el evento.',
            'Una vez aprobada la cotización, u orden de compra y realizado el anticipo , NO se realizan devoluciones de los productos ni del dinero, en caso de no consumir todos los productos nuestros clientes podrán llevarselos o solicitar un acuerdo para realizar el consumo de los productos faltantes , ( Entiéndase productos NO perecederos ) en las fechas y ubicación que disponga PALOS DE LEÑA.',
            'El pago del anticipo del 50% debe ser consignado a la cuenta de palos de leña, el restante debe ser realizado en  el punto, recuerda que si confirmas entre hoy y mañana te brindamos el postre adicional sin costo.',
            'En caso de algún imprevisto , de ser postergado o cancelado el evento, es compromiso y responsabilidad de la persona, o empresa contratante informar por escrito como mínimo una semana antes para evitar perdidas y frustaciones del mismo. De no dar aviso en el tiempo reglamentario, PALOS DE LEÑA, hará la devolución  del 30% UNICAMENTE, correspondiente al anticipo.',
            'Los precios de los productos estan sujetos a cambios pasado 10 dias de enviarse la cotización, ya que nuestros precios pueden varias por temporadas',
            'La vigencia de la cotizacion enviada es de 7 a 10 dias habiles.',
            'El establecimiento NO cuenta con parqueadero, ni tiene convenio con ninguno del sector,pero cerca a nuestros puntos encuentran parqueaderos.',
            'Aplican retenciones según CIIU 5611: 3.5% por servicios de restaurante y 13.80‰ de reteICA.',
        ];

        foreach ($terms as $index => $term) {
            $sheet->mergeCells('A' . $r . ':F' . $r);
            $sheet->setCellValue('A' . $r, ($index + 1) . '. ' . $term);
            // Enable text wrapping for long terms
            $sheet->getStyle('A' . $r)->getAlignment()->setWrapText(true);
            // Set font size to 9pt for terms
            $sheet->getStyle('A' . $r)->getFont()->setSize(9);
            $r++;
        }

        // Store the last row with terms for later use
        $lastRowWithTerms = $r - 1;

        // Apply bold to totals area only (excluding terms and conditions)
        $sheet->getStyle('A' . ($subtotalRow) . ':F' . $lastTotalRow)->getFont()->setBold(true);

        // Number format for column D (Cantidad) - no currency symbol, just number
        $sheet->getStyle('D' . $startProductsRow . ':D' . ($r - 1))->getNumberFormat()->setFormatCode('#,##0');
        
        // Number format for columns E and F (prices and totals) with currency symbol
        $sheet->getStyle('E' . $startProductsRow . ':F' . ($r - 1))->getNumberFormat()->setFormatCode('$#,##0.00');

        // Apply percentage format to discount (%) row if it exists
        if ($descuentoPctRow !== null) {
            $sheet->getStyle('F' . $descuentoPctRow)->getNumberFormat()->setFormatCode('0"%"');
        }

        // Apply center alignment to all cells
        $sheet->getStyle('A15:F' . ($r - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A15:F' . ($r - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Apply black borders to all cells
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:F' . ($r - 1))->applyFromArray($styleArray);

        // Set column widths to prevent border cutting in PDF
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);

        // Set row height for logo area (rows 1-14) to accommodate the image
        // Different heights for PDF vs Excel to avoid overlap and maintain proportions
        $rowHeight = $forPdf ? 115 : 15;
        for ($i = 1; $i <= 14; $i++) {
            $sheet->getRowDimension($i)->setRowHeight($rowHeight);
        }

        return $spreadsheet;
    }

    public function edit($id)
    {
        $cot = Cotizacion::with(['items.producto', 'itemExtras'])->findOrFail($id);
        $extras = ItemExtras::all();
        $clientes = Cliente::orderBy('created_at', 'desc')->get();
        $productos = Productos::whereIn('proTipo', ['Carta-E', 'Carta-F', 'Carta-P', 'Carta-B'])->get();
        $sedes = Puntos::whereNotIn('nombre', ['Planta', 'Administrativo', 'Cocina', 'Parrilla'])->get();

        return view('cotizaciones.edit', compact('cot', 'extras', 'clientes', 'productos', 'sedes'));
    }

    // Begin update flow for cotizacion
    public function update(Request $request, $id)
    {
        $cot = Cotizacion::findOrFail($id);

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'motivo' => 'required|string',
            'sede' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
            'numero_personas' => 'required|integer|min:1',

            'subtotal' => 'required|numeric',
            'ipoconsumo' => 'nullable|numeric',

            'descuento_pct' => 'nullable|numeric',
            'descuento_monto' => 'nullable|numeric',

            'reteica' => 'nullable|numeric',
            'retefuente' => 'nullable|numeric',

            'propina_aplicado' => 'nullable|numeric',
            'anticipo_aplicado' => 'nullable|numeric',

            'total_final' => 'required|numeric',
            'total_pendiente' => 'nullable|numeric',

            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'items.*.total_item' => 'required|numeric|min:0',

            // Validación para items extras
            'extras' => 'nullable|array',
            'extras.*.item_extra_id' => 'required|string',
            'extras.*.nombre_custom' => 'nullable|string|max:255',
            'extras.*.cantidad' => 'nullable|integer|min:1',
            'extras.*.valor' => 'required|numeric|min:0',
            'extras.*.suma_al_total' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Logic similar to store for totals
            $subtotal = (float) $validated['subtotal'];
            $descuentoPct = isset($validated['descuento_pct']) ? (float)$validated['descuento_pct'] : 0;
            $descuentoMontoTotal = isset($validated['descuento_monto']) ? (float)$validated['descuento_monto'] : 0;

            if ($descuentoPct > 0 && !empty($descuentoMontoTotal)) {
                $descuentoMonto = $subtotal - $descuentoMontoTotal;
            } else {
                $descuentoMonto = 0;
            }
            $subtotal_menos_descuento = ($descuentoPct > 0) ? $descuentoMontoTotal : $subtotal;

            $cot->update([
                'cliente_id' => $validated['cliente_id'],
                'motivo' => $validated['motivo'],
                'sede' => $validated['sede'],
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'numero_personas' => $validated['numero_personas'],

                'subtotal' => $subtotal,
                'ipoconsumo' => isset($validated['ipoconsumo']) ? (float)$validated['ipoconsumo'] : 0,

                'descuento_pct' => $descuentoPct,
                'descuento_monto' => $descuentoMonto,
                'subtotal_menos_descuento' => $subtotal_menos_descuento,

                'reteica' => isset($validated['reteica']) ? (float)$validated['reteica'] : 0,
                'retefuente' => isset($validated['retefuente']) ? (float)$validated['retefuente'] : 0,

                'propina' => isset($validated['propina_aplicado']) ? (float)$validated['propina_aplicado'] : 0,
                'total_final' => (float)$validated['total_final'],
                'anticipo' => isset($validated['anticipo_aplicado']) ? (float)$validated['anticipo_aplicado'] : 0,
                'saldo_pendiente' => isset($validated['total_pendiente']) ? (float)$validated['total_pendiente'] : 0,
            ]);

            // Sync Items: Delete all and recreate to ensure exact match
            $cot->items()->delete();
            foreach ($validated['items'] as $item) {
                 CotizacionItem::create([
                    'cotizacion_id' => $cot->id,
                    'producto_id' => $item['producto_id'],
                    'producto_precio' => (float)$item['precio'],
                    'cantidad' => (int)$item['cantidad'],
                    'total_item' => (float)$item['total_item'],
                ]);
            }

            // Sync Extras
            $cot->itemExtras()->detach(); // Remove all existing
            if (isset($validated['extras']) && is_array($validated['extras'])) {
                foreach ($validated['extras'] as $extra) {
                    $itemExtraId = null;
                    $nombre = '';
                    
                    if ($extra['item_extra_id'] === 'custom') {
                        $nombre = $extra['nombre_custom'] ?? 'Item Extra';
                        $nuevoExtra = ItemExtras::firstOrCreate(
                            ['nombre' => $nombre],
                            ['precio' => (float)$extra['valor']]
                        );
                        $itemExtraId = $nuevoExtra->id;
                    } else {
                        $itemExtraId = (int)$extra['item_extra_id'];
                        $itemExtra = ItemExtras::find($itemExtraId);
                        $nombre = $itemExtra ? $itemExtra->nombre : 'Item Extra';
                    }
                    
                    $cot->itemExtras()->attach($itemExtraId, [
                        'nombre' => $nombre,
                        'cantidad' => isset($extra['cantidad']) ? (int)$extra['cantidad'] : 1,
                        'valor' => (float)$extra['valor'],
                        'suma_al_total' => isset($extra['suma_al_total']) ? true : false,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('coti.show', $cot->id)->with('success', 'Cotización actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando cotización', ['exception' => $e]);
            return back()->withInput()->withErrors(['error' => 'Error al actualizar la cotización. ' . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created cotizacion and its items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'motivo' => 'required|string',
            'sede' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
            'numero_personas' => 'required|integer|min:1',

            'subtotal' => 'required|numeric',
            'ipoconsumo' => 'nullable|numeric',

            'descuento_pct' => 'nullable|numeric',
            'descuento_monto' => 'nullable|numeric',
            //'subtotal_menos_descuento' will be computed server-side

            'reteica' => 'nullable|numeric',
            'retefuente' => 'nullable|numeric',

            'propina_aplicado' => 'nullable|numeric',
            'anticipo_aplicado' => 'nullable|numeric',

            'total_final' => 'required|numeric',
            'total_pendiente' => 'nullable|numeric',

            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'items.*.total_item' => 'required|numeric|min:0',

            // Validación para items extras (opcional)
            'extras' => 'nullable|array',
            'extras.*.item_extra_id' => 'required|string',
            'extras.*.nombre_custom' => 'nullable|string|max:255',
            'extras.*.cantidad' => 'nullable|integer|min:1',
            'extras.*.valor' => 'required|numeric|min:0',
            'extras.*.suma_al_total' => 'nullable|boolean',
        ]);

        // Use DB transaction to ensure integrity
        DB::beginTransaction();
        try {
            // compute subtotal_menos_descuento if not provided
            $subtotal = (float) $validated['subtotal'];
            $descuentoPct = isset($validated['descuento_pct']) ? (float)$validated['descuento_pct'] : 0;
            $descuentoMontoTotal = isset($validated['descuento_monto']) ? (float)$validated['descuento_monto'] : 0;
            

            if ($descuentoPct > 0 && !empty($descuentoMontoTotal)) {
                $descuentoMonto = $subtotal - $descuentoMontoTotal;
            } else {
                $descuentoMonto = 0;
            }

            $subtotal_menos_descuento = ($descuentoPct > 0) ? $descuentoMontoTotal : $subtotal;

            $cotizacion = Cotizacion::create([
                'cliente_id' => $validated['cliente_id'],
                'motivo' => $validated['motivo'],
                'sede' => $validated['sede'],
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'numero_personas' => $validated['numero_personas'],

                'subtotal' => $subtotal,
                'ipoconsumo' => isset($validated['ipoconsumo']) ? (float)$validated['ipoconsumo'] : 0,

                'descuento_pct' => $descuentoPct,
                'descuento_monto' => $descuentoMonto,
                'subtotal_menos_descuento' => $subtotal_menos_descuento,

                'reteica' => isset($validated['reteica']) ? (float)$validated['reteica'] : 0,
                'retefuente' => isset($validated['retefuente']) ? (float)$validated['retefuente'] : 0,

                'propina' => isset($validated['propina_aplicado']) ? (float)$validated['propina_aplicado'] : 0,
                'total_final' => (float)$validated['total_final'],
                'anticipo' => isset($validated['anticipo_aplicado']) ? (float)$validated['anticipo_aplicado'] : 0,
                'saldo_pendiente' => isset($validated['total_pendiente']) ? (float)$validated['total_pendiente'] : 0,
            ]);

            // insert items
            foreach ($validated['items'] as $item) {
                CotizacionItem::create([
                    'cotizacion_id' => $cotizacion->id,
                    'producto_id' => $item['producto_id'],
                    'producto_precio' => (float)$item['precio'],
                    'cantidad' => (int)$item['cantidad'],
                    'total_item' => (float)$item['total_item'],
                ]);
            }


            // Guardar items extras si existen
            Log::info('Verificando items extras', ['extras' => $request->input('extras')]);
            
            if (isset($validated['extras']) && is_array($validated['extras'])) {
                Log::info('Items extras encontrados', ['count' => count($validated['extras'])]);
                
                foreach ($validated['extras'] as $extra) {
                    Log::info('Procesando item extra', ['extra' => $extra]);
                    
                    $itemExtraId = null;
                    $nombre = '';
                    
                    // Si es personalizado
                    if ($extra['item_extra_id'] === 'custom') {
                        $nombre = $extra['nombre_custom'] ?? 'Item Extra';
                        
                        // Guardar en catálogo para reutilización futura
                        $nuevoExtra = ItemExtras::firstOrCreate(
                            ['nombre' => $nombre],
                            ['precio' => (float)$extra['valor']]
                        );
                        $itemExtraId = $nuevoExtra->id;
                        Log::info('Item extra personalizado creado', ['id' => $itemExtraId, 'nombre' => $nombre]);
                    } else {
                        // Si es del catálogo
                        $itemExtraId = (int)$extra['item_extra_id'];
                        $itemExtra = ItemExtras::find($itemExtraId);
                        $nombre = $itemExtra ? $itemExtra->nombre : 'Item Extra';
                        Log::info('Item extra del catálogo', ['id' => $itemExtraId, 'nombre' => $nombre]);
                    }
                    
                    // Guardar en la tabla pivot usando attach
                    $cotizacion->itemExtras()->attach($itemExtraId, [
                        'nombre' => $nombre,
                        'cantidad' => isset($extra['cantidad']) ? (int)$extra['cantidad'] : 1,
                        'valor' => (float)$extra['valor'],
                        'suma_al_total' => isset($extra['suma_al_total']) ? true : false,
                    ]);
                    
                    Log::info('Item extra guardado en pivot', [
                        'cotizacion_id' => $cotizacion->id,
                        'item_extra_id' => $itemExtraId,
                        'suma_al_total' => isset($extra['suma_al_total'])
                    ]);
                }
            } else {
                Log::info('No se encontraron items extras en validated');
            }


            DB::commit();

            return redirect()->route('coti.index')->with('success', 'Cotización creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log full exception to laravel.log for debugging
            Log::error('Error creando cotización', ['exception' => $e]);
            return back()->withInput()->withErrors(['error' => 'Error al crear la cotización. Por favor intente nuevamente.']);
        }
    }

    /**
     * Delete a cotizacion and its related items.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $cot = Cotizacion::findOrFail($id);
            
            // Delete related items and extras (cascade delete should handle this, but being explicit)
            $cot->items()->delete();
            $cot->itemExtras()->detach();
            
            // Delete the cotizacion
            $cot->delete();
            
            DB::commit();
            return redirect()->route('coti.index')->with('success', 'Cotización eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando cotización', ['exception' => $e]);
            return back()->withErrors(['error' => 'Error al eliminar la cotización.']);
        }
    }

    /**
     * Upload payment receipt image for a cotizacion
     */
    public function uploadFactura(Request $request, $id)
    {
        $request->validate([
            'img_factura' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            $cot = Cotizacion::findOrFail($id);
            
            // Delete old image if exists
            if ($cot->img_factura && \Storage::disk('public')->exists('cotizaciones_facturas/' . $cot->img_factura)) {
                \Storage::disk('public')->delete('cotizaciones_facturas/' . $cot->img_factura);
            }
            
            // Store new image
            $file = $request->file('img_factura');
            $filename = 'factura_cot_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('cotizaciones_facturas', $filename, 'public');
            
            // Update database
            $cot->img_factura = $filename;
            $cot->save();
            
            return redirect()->route('coti.index')->with('success', 'Comprobante de pago cargado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error cargando factura', ['exception' => $e]);
            return back()->withErrors(['error' => 'Error al cargar el comprobante.']);
        }
    }
}
