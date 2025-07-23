<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdenProduccionResource\Pages;
use App\Filament\Resources\OrdenProduccionResource\RelationManagers;
use App\Models\OrdenProduccion;
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
                    ->preload()
                    ->placeholder('Seleccione un responsable'),
                Forms\Components\Select::make('produccion_id')
                    ->relationship('producciones', 'produccion')
                    ->label('Producciones')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccione una Producción'),

            Forms\Components\Repeater::make('materias_primas')
                ->label('Materias Primas')
                ->relationship('producciones') // No guardar directamente, solo mostrar
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->label('Materia Prima')
                        ->disabled(),
                    Forms\Components\TextInput::make('unidad')
                        ->label('Unidad')
                        ->disabled(),
                    Forms\Components\TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->disabled(),
                ])
                ->default(function (callable $get) {
                    $produccionId = $get('produccion_id');
                    if (!$produccionId) return [];

                    $produccion = Produccion::with('materiasPrimas')->find($produccionId);

                    if (!$produccion) return [];

                    return $produccion->materiasPrimas->map(function ($producto) {
                        return [
                            'nombre' => $producto->proNombre,
                            'unidad' => $producto->proUnidadMedida,
                            'cantidad' => $producto->pivot->cantidad,
                        ];
                    })->toArray();
                })
                ->columns(3)
                ->disabled(), // Opcional: para evitar edición
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
