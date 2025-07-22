<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrazabilidadProductoResource\Pages;
use App\Filament\Resources\TrazabilidadProductoResource\RelationManagers;
use App\Models\TrazabilidadProducto;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput\Mask;

class TrazabilidadProductoResource extends Resource
{
    protected static ?string $model = TrazabilidadProducto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('traFechaMovimiento')
                    ->required()
                    ->label('Fecha de Movimiento'),
                Forms\Components\Select::make('traTipoMovimiento')
                    ->required()
                    ->label('Tipo de Movimiento')
                    ->options([
                        'Ingreso' => 'Ingreso',
                        'Egreso' => 'Egreso',
                        'Devolución' => 'Devolución'
                        ]),
                Forms\Components\Select::make('traIdProducto')
                    ->required()
                    ->relationship('producto', 'proNombre')
                    ->label('Producto')
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccione un producto'),
                Forms\Components\TextInput::make('traCantidad')
                    ->required()
                    ->numeric()
                    ->label('Cantidad')
                    ->extraInputAttributes(['inputmode' => 'decimal']) // para móviles
                    ->live() // actualiza en vivo sin formateo
                    ->rule('numeric') // validación a nivel de backend
                    ->formatStateUsing(fn ($state) => (float) str_replace(',', '.', str_replace('.', '', $state)))
                    ->dehydrateStateUsing(fn ($state) => (float) str_replace(',', '.', str_replace('.', '', $state))),
                Forms\Components\TextInput::make('traLoteSerie')
                    ->required()
                    ->maxLength(255)
                    ->label('Lote/Serie'),
                Forms\Components\TextInput::make('traObservaciones')
                    ->maxLength(500)
                    ->label('Observaciones'),
                Forms\Components\Select::make('traDestino')
                    ->required()
                    ->options([
                        'puente' => 'Puente Aranda'
                    ])
                    ->label('Almacén/Ubicación'),
                Forms\Components\TextInput::make('traResponsable')
                    ->required()
                    ->maxLength(255)
                    ->label('Responsable'),
                Forms\Components\Select::make('traColor')
                    ->label('Color del Producto')
                    ->options([
                        'bueno' => 'Bueno',
                        'malo' => 'Malo'
                    ])
                    ->default('bueno')
                    ->required(),
                Forms\Components\Select::make('traTextura')
                    ->label('Textura del Producto')
                    ->options([
                        'bueno' => 'Bueno',
                        'malo' => 'Malo'
                    ])
                    ->default('bueno')
                    ->required(),
                Forms\Components\Select::make('traOlor')
                    ->label('Olor del Producto')
                    ->options([
                        'bueno' => 'Bueno',
                        'malo' => 'Malo'
                    ])
                    ->default('bueno')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('traFechaMovimiento')->label('Fecha de Movimiento'),
                TextColumn::make('traTipoMovimiento')->label('Tipo de Movimiento'),
                TextColumn::make('producto.proNombre')->label('Producto'),
                TextColumn::make('traCantidad')
                    ->label('Cantidad')
                    ,
                TextColumn::make('traLoteSerie')->label('Lote/Serie'),
                TextColumn::make('traObservaciones')->label('Observaciones'),
                TextColumn::make('traDestino')->label('Almacén/Ubicación'),
                TextColumn::make('traResponsable')->label('Responsable'),
                TextColumn::make('traColor')->label('Color del Producto'),
                TextColumn::make('traTextura')->label('Textura del Producto'),
                TextColumn::make('traOlor')->label('Olor del Producto'),
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
            'index' => Pages\ListTrazabilidadProductos::route('/'),
            'create' => Pages\CreateTrazabilidadProducto::route('/create'),
            'edit' => Pages\EditTrazabilidadProducto::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $usuario = auth()->user();
        $cargo = $usuario?->cargo?->car_nombre;

        return in_array($cargo, ['Administrador', 'Planta']);
    }
}
