<?php

namespace App\Filament\Resources\PuntosResource\Pages;

use App\Filament\Resources\PuntosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPuntos extends ListRecords
{
    protected static string $resource = PuntosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
