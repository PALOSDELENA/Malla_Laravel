<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CargosController extends Controller
{
    public function index()
    {
        $cargos = \App\Models\Cargos::orderBy('id', 'asc')->paginate(10);
        return view('admin_cargos.cargos', compact('cargos'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_nombre' => 'required|string|max:255',
        ]);

        \App\Models\Cargos::create($validated);

        return redirect()->route('cargos.index')->with('success', 'Cargo creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'car_nombre' => 'required|string|max:255',
        ]);

        $cargo = \App\Models\Cargos::findOrFail($id);
        $cargo->car_nombre = $validated['car_nombre'];
        $cargo->save();

        return redirect()->route('cargos.index')->with('success', 'Cargo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $cargo = \App\Models\Cargos::findOrFail($id);
        $cargo->delete();

        return redirect()->route('cargos.index')->with('success', 'Cargo eliminado correctamente.');
    }
 }
