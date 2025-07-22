<?php

namespace App\Filament\Pages;

use App\Models\Asignacion_Turnos;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Filament\Pages\KanbanBoardPage;
use Filament\Actions\Action;
use Filament\Forms;
use App\Models\Turnos;
use App\Models\Usuarios;

class TurnosKanbanBoardPage extends KanbanBoardPage
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Turnos Kanban Board Page';
    protected static ?string $title = 'AsignacionTurnos Board';

    public function getSubject(): Builder
    {
        // return Asignacion_Turnos::query();
        return Asignacion_Turnos::with(['turno', 'usuario']);
    }

    public function mount(): void
    {
        $this
            ->titleField('titulo_card')
            ->orderField('order_column')
            ->columnField('dia')
            ->columns([
                'Lunes' => 'Lunes',
                'Martes' => 'Martes',
                'Miércoles' => 'Miércoles',
                'Jueves' => 'Jueves',
                'Viernes' => 'Viernes',
                'Sábado' => 'Sábado',
                'Domingo' => 'Domingo',
            ])
            ->columnColors([
                'Lunes' => 'blue',
                'Martes' => 'indigo',
                'Miércoles' => 'violet',
                'Jueves' => 'fuchsia',
                'Viernes' => 'green',
                'Sábado' => 'yellow',
                'Domingo' => 'rose',
            ]);
    }
    
protected function getCardAttributes(): array
{
    return [
        'Detalle' => function ($record) {
            return $record->usuario?->usu_nombre . '<br><small>' . $record->turno?->tur_nombre . '</small>';
        },
    ];
}

    public function createAction(Action $action): Action
    {
        return $action
            ->iconButton()
            ->icon('heroicon-o-plus')
            ->modalHeading('Nueva Asignación de Turno')
            ->modalWidth('xl')
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Forms\Components\Select::make('usuarios_num_doc')
                        ->label('Empleado')
                        ->options(Usuarios::all()->pluck('usu_nombre', 'num_doc'))
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('turnos_id')
                        ->label('Turno')
                        ->options(
                            Turnos::all()->mapWithKeys(function ($turno) {
                                return [
                                    $turno->id_turnos => "{$turno->tur_nombre} – {$turno->tur_descripcion}",
                                ];
                            })
                        )
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre para la Tarjeta')
                        ->default(fn () => 'Nuevo turno')
                        ->required(),

                    Forms\Components\Select::make('dia')
                        ->label('Día del Panel (Columna)')
                        ->options([
                            'Lunes' => 'Lunes',
                            'Martes' => 'Martes',
                            'Miércoles' => 'Miércoles',
                            'Jueves' => 'Jueves',
                            'Viernes' => 'Viernes',
                            'Sábado' => 'Sábado',
                            'Domingo' => 'Domingo',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('tur_usu_dia')
                        ->label('Día lógico del Turno')
                        ->default('Lunes')
                        ->required(),

                    Forms\Components\DatePicker::make('tur_usu_fecha')
                        ->label('Fecha del Turno')
                        ->required(),
                ]);
            });
    }

    public function editAction(Action $action): Action
    {
        return $action
            ->modalHeading('Editar Asignación')
            ->modalWidth('xl')
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Forms\Components\Select::make('usuarios_num_doc')
                        ->label('Empleado')
                        ->options(
                            Usuarios::all()->mapWithKeys(fn ($u) => [
                                $u->num_doc => "{$u->usu_nombre} {$u->usu_apellido}"
                            ])
                        )
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('turnos_id')
                        ->label('Turno')
                        ->options(
                            Turnos::all()->mapWithKeys(fn ($t) => [
                                $t->id_turnos => "{$t->tur_nombre} – {$t->tur_descripcion}"
                            ])
                        )
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Título visible')
                        ->required(),

                    Forms\Components\Select::make('dia')
                        ->label('Día del tablero')
                        ->options([
                            'Lunes' => 'Lunes',
                            'Martes' => 'Martes',
                            'Miércoles' => 'Miércoles',
                            'Jueves' => 'Jueves',
                            'Viernes' => 'Viernes',
                            'Sábado' => 'Sábado',
                            'Domingo' => 'Domingo',
                        ])
                        ->required(),

                    Forms\Components\DatePicker::make('tur_usu_fecha')
                        ->label('Fecha del turno')
                        ->required(),
                ]);
            });
    }

    public static function shouldRegisterNavigation(): bool
    {
        $usuario = auth()->user();
        $cargo = $usuario?->cargo?->car_nombre;

        return in_array($cargo, ['Administrador']);
    }
}
