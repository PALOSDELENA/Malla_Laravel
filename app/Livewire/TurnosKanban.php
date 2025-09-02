<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asignacion_Turnos;
use App\Models\User;
use App\Models\Turnos;
use Livewire\Attributes\On;

class TurnosKanban extends Component
{
    public $columns = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    public $asignaciones = [];

    public $modalOpen = false;
    public $editing = false;

    public $form = [
        'id' => null,
        'usuarios_num_doc' => '',
        'turnos_id' => '',
        'nombre' => '',
        'dia' => 'Lunes',
        'tur_usu_fecha' => '',
    ];

    public function mount()
    {
        $this->loadAsignaciones();
    }

    public function loadAsignaciones()
    {
        $this->asignaciones = Asignacion_Turnos::with(['usuario', 'turno'])
            ->get()
            ->groupBy('dia')
            ->map(fn($group) => $group->toArray()) // <-- convierte cada subgrupo a array
            ->toArray(); // <-- convierte todo el resultado a array
    }

    public function moverTurno($turnoId, $nuevoDia)    {
        $asignacion = Asignacion_Turnos::find($turnoId);

        if ($asignacion && $asignacion->dia !== $nuevoDia) {
            $asignacion->dia = $nuevoDia;
            $asignacion->save();
            $this->loadAsignaciones(); // recarga la matriz de columnas
        }
    }    
    public function openModal($id = null)
    {
        $this->reset('form');
        $this->editing = false;

        if ($id) {
            $this->editing = true;
            $record = Asignacion_Turnos::findOrFail($id);
            $this->form = $record->toArray();
        }

        $this->modalOpen = true;
    }

    public function save()
    {
        $data = $this->validate([
            'form.usuarios_num_doc' => 'required|exists:users,num_doc',
            'form.turnos_id' => 'required|exists:turnos,id_turnos',
            'form.nombre' => 'required|string',
            'form.dia' => 'required',
            'form.tur_usu_fecha' => 'required|date',
        ])['form'];

        Asignacion_Turnos::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        $this->modalOpen = false;
        $this->loadAsignaciones();
    }

    public function render()
    {
        return view('livewire.turnos-kanban', [
            'usuarios' => User::all(),
            'turnos' => Turnos::all(),
        ]);
    }
}

