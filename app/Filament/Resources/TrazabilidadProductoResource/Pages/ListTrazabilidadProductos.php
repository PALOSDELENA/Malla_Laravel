<?php

namespace App\Filament\Resources\TrazabilidadProductoResource\Pages;

use App\Filament\Resources\TrazabilidadProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrazabilidadProductos extends ListRecords
{
    protected static string $resource = TrazabilidadProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
