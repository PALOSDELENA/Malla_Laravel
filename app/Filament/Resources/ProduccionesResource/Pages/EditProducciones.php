<?php

namespace App\Filament\Resources\ProduccionesResource\Pages;

use App\Filament\Resources\ProduccionesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProducciones extends EditRecord
{
    protected static string $resource = ProduccionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
