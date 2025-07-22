<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduccionesResource\Pages;
use App\Filament\Resources\ProduccionesResource\RelationManagers;
use App\Models\Producciones;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Productos;
class ProduccionesResource extends Resource
{
    protected static ?string $model = Producciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('produccion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('tiempo_min')
                    ->required()
                    ->label('Tiempo de Producción (minutos)')
                    ->options([
                        '30' => '30 minutos',
                        '60' => '1 hora',
                        '120' => '2 horas',
                        '240' => '4 horas'
                    ])
                    ->placeholder('Seleccione un tiempo de producción')
                    ->default('60'),
                Repeater::make('materiasPrimas')
                    ->relationship('materiasPrimas') // nombre del método en el modelo Producciones
                    ->label('Materias Primas')
                    ->schema([
                        Select::make('m_prima_id') // clave foránea en tabla pivote
                            ->label('Materia Prima')
                            ->options(Productos::where('proTipo', 'Materia Prima')->pluck('proNombre', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->createItemButtonLabel('Agregar Materia Prima')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('produccion')
                    ->label('Producción'),
                TextColumn::make('tiempo_min')
                                ->label('Tiempo de Producción (minutos)'),
TextColumn::make('materiasPrimas')
    ->label('Materias Primas')
    ->getStateUsing(function ($record) {
        return $record->materiasPrimas
            ->map(function ($item) {
                return $item->producto->proNombre . ' (' . $item->cantidad . ')';
            })
            ->implode(', ');
    }),
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
            'index' => Pages\ListProducciones::route('/'),
            'create' => Pages\CreateProducciones::route('/create'),
            'edit' => Pages\EditProducciones::route('/{record}/edit'),
        ];
    }
}
