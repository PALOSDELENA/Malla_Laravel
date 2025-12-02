<?php

namespace App\Filament\Resources\CargosResource\Pages;

use App\Filament\Resources\CargosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCargos extends EditRecord
{
    protected static string $resource = CargosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
