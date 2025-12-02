<?php

namespace App\Filament\Resources\OrdenProduccionResource\Pages;

use App\Filament\Resources\OrdenProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\TrazabilidadProducto;
use Illuminate\Support\Facades\DB;
use App\Models\OrdenProduccion;


class EditOrdenProduccion extends EditRecord
{
    protected static string $resource = OrdenProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $materias = $this->form->getState()['materias_primas'] ?? [];
        $ordenId = $this->record->getKey();

        DB::transaction(function () use ($materias, $ordenId) {
            // Eliminar registros anteriores de trazabilidad para esta orden
            TrazabilidadProducto::where('orden_produccion_id', $ordenId)->delete();

            foreach ($materias as $materia) {
                TrazabilidadProducto::create([
                    'orden_produccion_id' => $ordenId,
                    'traFechaMovimiento' => now(),
                    'traTipoMovimiento' => 'Consumo Interno',
                    'traIdProducto' => $materia['producto_id'],
                    'traCantidad' => $materia['cantidad_real'],
                    'traResponsable' => auth()->user()->num_doc ?? '0000',
                    'traObservaciones' => 'Consumo de materia prima en edición de orden de producción',
                ]);
            }
        });
    }
}