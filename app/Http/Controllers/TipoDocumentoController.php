<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        $tDocumentos = \App\Models\Tipo_Documento::orderBy('id', 'asc')->paginate(10);
        return view('admin_tDocumentos.documentos', compact('tDocumentos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|string|max:255',
        ]);

        \App\Models\Tipo_Documento::create($validated);

        return redirect()->route('tipos-documentos.index')->with('success', 'Tipo de documento creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|string|max:255',
        ]);

        $tipoDocumento = \App\Models\Tipo_Documento::findOrFail($id);
        $tipoDocumento->tipo_documento = $validated['tipo_documento'];
        $tipoDocumento->save();

        return redirect()->route('tipos-documentos.index')->with('success', 'Tipo de documento actualizado correctamente.');
    }

    public function destroy($id)
    {
        $tipoDocumento = \App\Models\Tipo_Documento::findOrFail($id);
        $tipoDocumento->delete();

        return redirect()->route('tipos-documentos.index')->with('success', 'Tipo de documento eliminado correctamente.');
    }
}
