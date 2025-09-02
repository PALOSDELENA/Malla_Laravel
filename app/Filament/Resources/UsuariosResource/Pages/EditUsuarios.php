<?php

namespace App\Filament\Resources\UsuariosResource\Pages;

use App\Filament\Resources\UsuariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsuarios extends EditRecord
{
    protected static string $resource = UsuariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $clave = $this->form->getRawState()['clave_plana'] ?? null;

        if ($clave && $clave !== null) {
            $this->record->seguridad()->updateOrCreate(
                ['seg_usuario_id' => $this->record->num_doc],
                ['seg_credencial' => \Hash::make($clave)]
            );
        }
    }
}
