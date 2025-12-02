<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PuntosResource\Pages;
use App\Filament\Resources\PuntosResource\RelationManagers;
use App\Models\Puntos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PuntosResource extends Resource
{
    protected static ?string $model = Puntos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre del Punto'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('nombre')->label('Nombre del Punto'),])
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
            'index' => Pages\ListPuntos::route('/'),
            'create' => Pages\CreatePuntos::route('/create'),
            'edit' => Pages\EditPuntos::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $usuario = auth()->user();
        $cargo = $usuario?->cargo?->car_nombre;

        return in_array($cargo, ['Administrador']);
    }
}
