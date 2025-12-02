<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CargosResource\Pages;
use App\Filament\Resources\CargosResource\RelationManagers;
use App\Models\Cargos;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CargosResource extends Resource
{
    protected static ?string $model = Cargos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('car_nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre del Cargo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('car_nombre')->label('Nombre del Cargo'),
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
            'index' => Pages\ListCargos::route('/'),
            'create' => Pages\CreateCargos::route('/create'),
            'edit' => Pages\EditCargos::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $usuario = auth()->user();
        $cargo = $usuario?->cargo?->car_nombre;

        return in_array($cargo, ['Administrador']);
    }
}
