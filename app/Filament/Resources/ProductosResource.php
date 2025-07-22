<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductosResource\Pages;
use App\Filament\Resources\ProductosResource\RelationManagers;
use App\Models\Productos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;

class ProductosResource extends Resource
{
    protected static ?string $model = Productos::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('proNombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre del Producto'),
                Forms\Components\Select::make('proUnidadMedida')
                    ->required()
                    ->label('Unidad de Medida')
                    ->options([
                        'Kilogramo' => 'Kilogramo',
                        'Gramo' => 'Gramo',
                        'Litro' => 'Litro',
                        'Unidad' => 'Unidad'
                    ])
                    ->placeholder('Seleccione una unidad de medida')
                    ->default('Gramo'),
                Forms\Components\Select::make('proTipo')
                    ->options([
                        'Materia Prima' => 'Materia Prima',
                        'Producto Terminado' => 'Producto Terminado'
                    ])
                    ->required()
                    ->label('Tipo de Producto'),
                Forms\Components\TagsInput::make('proListaIngredientes')
                    ->label('Ingredientes')
                    ->separator(',') // opcional, ya guarda como array
                    ->placeholder('Escribe y presiona Enter')
                    ->splitKeys(['Enter', ',']) // qué teclas separan los valores
                    ->suggestions(['Harina', 'Azúcar', 'Sal', 'Huevo', 'Leche']),
                Forms\Components\TextInput::make('proCondicionesConservacion')
                    ->maxLength(255)
                    ->label('Condiciones de Conservación'),
                Forms\Components\TextInput::make('proFabricante')
                    ->maxLength(255)
                    ->label('Fabricante')
                    ->default('Palos de Leña'),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('proNombre')->label('Nombre del Producto'),
                TextColumn::make('proUnidadMedida')->label('Unidad de Medida'),
                TextColumn::make('proTipo')->label('Tipo de Producto'),
                TextColumn::make('proListaIngredientes')->label('Ingredientes'),
                TextColumn::make('proCondicionesConservacion')->label('Condiciones de Conservación'),
                TextColumn::make('proFabricante')->label('Fabricante'),
            ])
            ->filters([
                Filter::make('proNombre')
                        ->form([
                            Forms\Components\TextInput::make('value')
                                ->label('Nombre'),
                        ])
                        ->query(function ($query, array $data) {
                            return $query
                                ->when($data['value'], fn ($q, $value) => $q->where('proNombre', 'like', "%{$value}%"));
                        }),

                Filter::make('proUnidadMedida')
                    ->form([
                        Forms\Components\TextInput::make('value')
                            ->label('Unidad de Medida'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['value'], fn ($q, $value) => $q->where('proUnidadMedida', 'like', "%{$value}%"));
                    }),            ])
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProductos::route('/create'),
            'edit' => Pages\EditProductos::route('/{record}/edit'),
        ];
    }
}
