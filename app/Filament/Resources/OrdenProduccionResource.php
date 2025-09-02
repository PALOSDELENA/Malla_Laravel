<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdenProduccionResource\Pages;
use App\Filament\Resources\OrdenProduccionResource\RelationManagers;
use App\Models\OrdenProduccion;
use App\Models\Producciones;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

class OrdenProduccionResource extends Resource
{
    protected static ?string $model = OrdenProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('responsable')
                    ->label('Responsable')
                    ->relationship('responsable1', 'usu_nombre')
                    ->required()
                    ->searchable()
                    ->placeholder('Seleccione un responsable'),
                Forms\Components\Select::make('produccion_id')
                        ->relationship('producciones', 'produccion')
                        ->label('Producción')
                        ->required()
                        ->searchable()
                        ->placeholder('Seleccione una Producción')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $produccion = Producciones::with('materiasPrimas.producto')->find($state);

                            if (!$produccion) {
                                $set('materias_primas', []);
                                return;
                            }

                            $set('materias_primas', $produccion->materiasPrimas->map(function ($relacion) {
                                return [
                                    'producto_id' => $relacion->producto->id,
                                    'nombre' => $relacion->producto->proNombre,
                                    'unidad' => $relacion->producto->proUnidadMedida ?? '',
                                    'cantidad_real' => null,
                                ];
                            })->toArray());
                        }),

                Forms\Components\Repeater::make('materias_primas')
                    ->label('Consumo de Materias Primas')
                    ->schema([
                        Forms\Components\Hidden::make('producto_id'),
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->disabled(),
                        Forms\Components\TextInput::make('unidad')
                            ->label('Unidad')
                            ->disabled(),
                        Forms\Components\TextInput::make('cantidad_real')
                            ->label('Cantidad Consumida')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->default([])
                    ->columns(2),
                Forms\Components\TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('fecha_fin')
                    ->label('Fecha de Fin'),
                Forms\Components\TextInput::make('estado')          
                    ->label('Estado')
                    ->required()
                    ->default('Pendiente')
                    ->maxLength(50),
                Forms\Components\TextInput::make('novedadProduccion')
                    ->label('Novedades')
                    ->maxLength(255)
                    ->placeholder('Ingrese novedades si es necesario'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('responsable1.usu_nombre')
                    ->label('Responsable')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('producciones.produccion')
                    ->label('Producción')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->date()
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->label('Fecha de Fin')
                    ->date()
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('novedadProduccion')
                    ->label('Novedades')
                    ->searchable()
                    ->limit(50),
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
            'index' => Pages\ListOrdenProduccions::route('/'),
            'create' => Pages\CreateOrdenProduccion::route('/create'),
            'edit' => Pages\EditOrdenProduccion::route('/{record}/edit'),
        ];
    }
}
