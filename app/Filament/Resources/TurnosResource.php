<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TurnosResource\Pages;
use App\Filament\Resources\TurnosResource\RelationManagers;
use App\Models\Turnos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TurnosResource extends Resource
{
    protected static ?string $model = Turnos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tur_nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre del Turno'),
                Forms\Components\TextInput::make('tur_descripcion')
                    ->required()
                    ->maxLength(255)
                    ->label('Descripción del Turno'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_turnos')->label('ID'),
                TextColumn::make('tur_nombre')->label('Turno'),
                TextColumn::make('tur_descripcion')->label('Descripción'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTurnos::route('/'),
            'create' => Pages\CreateTurnos::route('/create'),
            'edit' => Pages\EditTurnos::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $usuario = auth()->user();
        $cargo = $usuario?->cargo?->car_nombre;

        return in_array($cargo, ['Administrador']);
    }
}
