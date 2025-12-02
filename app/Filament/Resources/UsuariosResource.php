<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuariosResource\Pages;
use App\Filament\Resources\UsuariosResource\RelationManagers;
use App\Models\User;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UsuariosResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function eagerLoadRelations(): array
    {
        return ['seguridad'];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('t_doc')
                    ->relationship('tipoDocumento', 'tipo_documento')
                    ->required()
                    ->label('Tipo de Documento'),
                Forms\Components\TextInput::make('num_doc')
                    ->required()
                    ->maxLength(255)
                    ->label('Número de Documento')
                    ->autocomplete('off'),
                Forms\Components\TextInput::make('usu_nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre')
                    ->autocomplete('off'),
                Forms\Components\TextInput::make('usu_apellido')
                    ->required()
                    ->maxLength(255)
                    ->label('Apellido')
                    ->autocomplete('off'),
                Forms\Components\TextInput::make('usu_celular')
                    ->tel()
                    ->maxLength(20)
                    ->label('Teléfono')
                    ->autocomplete('off'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('Email')
                    ->autocomplete('new-email'),
                Forms\Components\Select::make('usu_cargo')
                    ->relationship('cargo', 'car_nombre') // ← Ajusta 'nombre' según tu modelo Cargo
                    ->label('Cargo')
                    ->required(),
                Forms\Components\Select::make('usu_punto')
                    ->relationship('punto', 'nombre') // ← Ajusta 'nombre' según tu modelo Punto
                    ->label('Punto de Trabajo')
                    ->required(),
                Forms\Components\TextInput::make('clave_plana')
                    ->label('Clave (Solo para usuarios que accederán al sistema.)')
                    ->password()
                    // ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrated(false) // hace que el valor no se envíe
                    ->afterStateHydrated(fn ($component) => $component->state(''))
                    ->autocomplete('new-password')
                    ->autocomplete('off'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('num_doc')->label('# Documento'),
                TextColumn::make('usu_nombre')->label('Nombre'),
                TextColumn::make('usu_apellido')->label('Apellido'),
                TextColumn::make('usu_celular')->label('Teléfono'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('cargo.car_nombre')->label('Cargo'), // Ajusta 'car_nombre' según tu modelo Cargo
                TextColumn::make('punto.nombre')->label('Punto de Trabajo'), // Ajusta 'nombre' según tu modelo Punto
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
            'index' => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuarios::route('/create'),
            'edit' => Pages\EditUsuarios::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $usuario = auth()->user();
        $cargo = $usuario?->cargo?->car_nombre;

        return in_array($cargo, ['Administrador']);
    }
}
