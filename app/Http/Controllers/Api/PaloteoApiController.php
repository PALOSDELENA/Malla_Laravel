<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Productos;
use Illuminate\Http\Request;

class PaloteoApiController extends Controller
{
    public function asignarSeccion(Request $request, $id)
    {
        $producto = Productos::findOrFail($id);

        $producto->proSeccion = $request->seccion_id; 
        $producto->save();

        return response()->json([
            'message' => 'Sección asignada correctamente',
            'producto' => $producto
        ]);
    }

    public function quitarSeccion($id)
    {
        $producto = Productos::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $producto->proSeccion = NULL; 
        $producto->save();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
    }
}
