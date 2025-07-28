<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\TrazabilidadProducto;
use App\Models\User;
use App\Models\Usuarios;
use Illuminate\Http\Request;

class TrazabilidadController extends Controller
{
    public function index()
    {
        $movimientos = TrazabilidadProducto::with(['producto', 'responsable'])
            ->orderBy('traFechaMovimiento', 'desc')
            ->paginate(10);
        $productos = Productos::orderBy('proNombre')->get();
        $usuarios = User::orderBy('usu_nombre')->get();

        return view('admin_trazabilidad.trazabilidad', compact('movimientos', 'productos', 'usuarios'));
    }
    public function create()
    {
        $productos = Productos::orderBy('proNombre')->get();
        $usuarios = User::orderBy('usu_nombre')->get();

        return view('admin_trazabilidad.create', compact('productos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'traFechaMovimiento' => 'required|date',
            'traTipoMovimiento' => 'required|string',
            'traIdProducto' => 'required|exists:productos,id',
            'traCantidad' => 'required|numeric',
            'traLoteSerie' => 'required|string|max:255',
            'traObservaciones' => 'nullable|string|max:500',
            'traDestino' => 'required|string',
            'traResponsable' => 'required|exists:users,num_doc',
            'traColor' => 'required|string',
            'traTextura' => 'required|string',
            'traOlor' => 'required|string',
        ]);

        TrazabilidadProducto::create($request->all());

        return redirect()->route('trazabilidad.index')->with('success', 'Movimiento registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'traFechaMovimiento' => 'required|date',
            'traTipoMovimiento' => 'required|string|in:Ingreso,Egreso,Devolución',
            'traIdProducto' => 'required|exists:productos,id',
            'traCantidad' => 'required|numeric|min:0',
            'traLoteSerie' => 'required|string|max:255',
            'traDestino' => 'required|string|max:255',
            'traResponsable' => 'required|exists:users,num_doc',
            'traColor' => 'required|in:Bueno,Malo',
            'traTextura' => 'required|in:Bueno,Malo',
            'traOlor' => 'required|in:Bueno,Malo',
            'traObservaciones' => 'nullable|string|max:500',
        ]);

        $traza = TrazabilidadProducto::findOrFail($id);
        $traza->update($request->all());

        return redirect()->route('trazabilidad.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $traza = TrazabilidadProducto::findOrFail($id);
        $traza->delete();

        return redirect()->route('trazabilidad.index')->with('success', 'Registro eliminado correctamente.');
    }
}
