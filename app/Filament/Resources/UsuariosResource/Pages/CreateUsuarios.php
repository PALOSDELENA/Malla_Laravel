<?php

namespace App\Filament\Resources\UsuariosResource\Pages;

use App\Filament\Resources\UsuariosResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUsuarios extends CreateRecord
{
    protected static string $resource = UsuariosResource::class;

    protected function afterCreate(): void
    {
        $clave = $this->form->getRawState()['clave_plana'] ?? null;
        if ($clave && $clave !== null) {
            $this->record->seguridad()->create([
                'seg_credencial' => \Hash::make($clave),
            ]);
        }
    }
}
