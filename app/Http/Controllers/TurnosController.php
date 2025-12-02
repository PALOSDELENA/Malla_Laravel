<?php

namespace App\Http\Controllers;

use App\Models\Turnos;
use Illuminate\Http\Request;

class TurnosController extends Controller
{
    public function index()
    {
        $turnos = Turnos::paginate(10); // Assuming you have a Turno model
        return view('admin_turnos.turnos', compact('turnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'turno_nombre' => 'required|string|max:255',
            'turno_descripcion' => 'required|string|max:255',
        ]);

        Turnos::create([
            'tur_nombre' => $request->turno_nombre,
            'tur_descripcion' => $request->turno_descripcion,
        ]);

        return redirect()->route('turnos.index')->with('success', 'Turno creado correctamente.');
    }

    public function update(Request $request, Turnos $turno)
    {
        $request->validate([
            'turno_nombre' => 'required|string|max:255',
            'turno_descripcion' => 'required|string|max:255',
        ]);

        $turno->update([
            'tur_nombre' => $request->turno_nombre,
            'tur_descripcion' => $request->turno_descripcion,
        ]);

        return redirect()->route('turnos.index')->with('success', 'Turno actualizado correctamente.');
    }

    public function destroy(Turnos $turno)
    {
        $turno->delete();

        return redirect()->route('turnos.index')->with('success', 'Turno eliminado correctamente.');
    }
}
