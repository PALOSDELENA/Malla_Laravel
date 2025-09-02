<?php

namespace App\Filament\Resources\PuntosResource\Pages;

use App\Filament\Resources\PuntosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPuntos extends EditRecord
{
    protected static string $resource = PuntosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
