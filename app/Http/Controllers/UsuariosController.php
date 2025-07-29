<?php

namespace App\Http\Controllers;

use App\Models\Cargos;
use App\Models\Puntos;
use App\Models\Seguridad;
use App\Models\Tipo_Documento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['tipoDocumento', 'cargo', 'punto'])->get();
        $cargos = Cargos::all();
        $puntos = Puntos::all();

        return view('admin_users.index', compact('usuarios', 'cargos', 'puntos'));
    }

    public function create()
    {
        $cargos = Cargos::all();
        $puntos = Puntos::all();
        $tDocumentos = Tipo_Documento::all();

        return view('admin_users.create', compact('cargos', 'puntos', 'tDocumentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            't_doc'         => 'required|exists:tipo_documento,id',
            'num_doc'       => 'required|string|unique:users,num_doc',
            'usu_nombre'    => 'required|string|max:255',
            'usu_apellido'  => 'required|string|max:255',
            'usu_celular'   => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255|unique:users,email',
            'usu_cargo'     => 'required|exists:cargos,id',
            'usu_punto'     => 'required|exists:puntos,id',
        ]);

        $user = User::create([
            't_doc'         => $request->t_doc,
            'num_doc'       => $request->num_doc,
            'usu_nombre'    => $request->usu_nombre,
            'usu_apellido'  => $request->usu_apellido,
            'usu_celular'   => $request->usu_celular,
            'email'         => $request->email,
            'usu_cargo'     => $request->usu_cargo,
            'usu_punto'     => $request->usu_punto,
        ]);

        if ($request->filled('password')) {
            Seguridad::create([
                'seg_usuario_id' => $user->num_doc,
                'seg_credencial' => Hash::make($request->input('password')),
            ]);
        }
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, $num_doc)
    {
        $request->validate([
            'usu_nombre'    => 'required|string|max:255',
            'usu_apellido'  => 'required|string|max:255',
            'usu_celular'   => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'usu_cargo'     => 'required|exists:cargos,id',
            'usu_punto'     => 'required|exists:puntos,id',
        ]);

        $usuario = User::findOrFail($num_doc);

        $usuario->update([
            'usu_nombre'   => $request->usu_nombre,
            'usu_apellido' => $request->usu_apellido,
            'usu_celular'  => $request->usu_celular,
            'email'        => $request->email,
            'usu_cargo'    => $request->usu_cargo,
            'usu_punto'    => $request->usu_punto,
        ]);

        if ($request->filled('password')) {
            $clave = Seguridad::where('seg_usuario_id', $usuario->num_doc)->first();

            if (!$clave) {
                Seguridad::create([
                    'seg_usuario_id' => $usuario->num_doc,
                    'seg_credencial' => Hash::make($request->input('password')),
                ]);
            } else {
                $clave->update([
                    'seg_credencial' => Hash::make($request->input('password')),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
    }
}
