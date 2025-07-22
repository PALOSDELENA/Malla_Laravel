<?php

namespace App\Filament\Resources\TrazabilidadProductoResource\Pages;

use App\Filament\Resources\TrazabilidadProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrazabilidadProducto extends EditRecord
{
    protected static string $resource = TrazabilidadProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
