<?php

namespace App\Filament\Resources\OrdenProduccionResource\Pages;

use App\Filament\Resources\OrdenProduccionResource;
use App\Models\OrdenProduccion;
use App\Models\TrazabilidadProducto;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrdenProduccion extends CreateRecord
{
    protected static string $resource = OrdenProduccionResource::class;

protected array $materiasPrimas = [];

protected function beforeCreate(): void
{
    $this->materiasPrimas = $this->form->getComponent('materias_primas')?->getRawState() ?? [];
}

protected function handleRecordCreation(array $data): Model
{
    \Log::info('handleRecordCreation fue llamado en', $data);
    \Log::info('Materias primas recibidas en', $this->materiasPrimas);

    $orden = OrdenProduccion::create($data);

    foreach ($this->materiasPrimas as $materia) {
        if (empty($materia['producto_id']) || !is_numeric($materia['cantidad_real'])) {
            \Log::warning('Materia prima con datos incompletos.', $materia);
            continue;
        }

        TrazabilidadProducto::create([
            'traFechaMovimiento' => now(),
            'traTipoMovimiento' => 'salida',
            'traIdProducto' => $materia['producto_id'],
            'traCantidad' => $materia['cantidad_real'],
            'orden_produccion_id' => $orden->id,
            'traResponsable' => $data['responsable'] ?? null,
        ]);
    }

    return $orden;
}
}
