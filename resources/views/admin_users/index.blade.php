<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 text-green-600 font-semibold">
                        {{ session('success') }}
                    </div>
                @endif
                <a href="{{ route('usuarios.create') }}" class="btn btn-primary mb-3">{{__('New')}}</a>
                <table class="w-full table-auto border-collapse border border-gray-300 px-4 text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-4 py-2 border border-gray-300">T.Doc</th>
                            <th class="px-4 py-2 border border-gray-300"># Doc.</th>
                            <th class="px-4 py-2 border border-gray-300">Nombre</th>
                            <th class="px-4 py-2 border border-gray-300">Apellido</th>
                            <th class="px-4 py-2 border border-gray-300">Teléfono</th>
                            <th class="px-4 py-2 border border-gray-300">Email</th>
                            <th class="px-4 py-2 border border-gray-300">Cargo</th>
                            <th class="px-4 py-2 border border-gray-300">Punto</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-300">
                                    @if($usuario->tipoDocumento->tipo_documento === 'Cédula de Ciudadanía')
                                        C.C
                                    @else
                                        {{ $usuario->tipoDocumento->tipo_documento }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-300">{{ $usuario->num_doc }}</td>
                                <td class="px-4 py-2 border border-gray-300">{{ $usuario->usu_nombre }}</td>
                                <td class="px-4 py-2 border border-gray-300">{{ $usuario->usu_apellido }}</td>
                                <td class="px-4 py-2 border border-gray-300">{{ $usuario->usu_celular }}</td>
                                <td class="px-4 py-2 border border-gray-300">{{ $usuario->email }}</td>
                                <td class="px-4 py-2 border border-gray-300">{{ $usuario->cargo->car_nombre ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-300">{{ $usuario->punto->nombre ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-300">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal{{ $usuario->num_doc }}">
                                        <i class="fas fa-edit"></i>
                                    </button>                                    
                                </td>
                            </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="editarUsuarioModal{{ $usuario->num_doc }}" tabindex="-1" aria-labelledby="editarUsuarioLabel{{ $usuario->num_doc }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('usuarios.update', $usuario->num_doc) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                        <h5 class="modal-title" id="editarUsuarioLabel{{ $usuario->num_doc }}">Editar Usuario</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="usu_nombre" class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" name="usu_nombre" value="{{ $usuario->usu_nombre }}" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="usu_apellido" class="form-label">Apellido</label>
                                                    <input type="text" class="form-control" name="usu_apellido" value="{{ $usuario->usu_apellido }}" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="usu_celular" class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control" name="usu_celular" value="{{ $usuario->usu_celular }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" value="{{ $usuario->email }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="usu_cargo" class="form-label">Cargo</label>
                                                    <select name="usu_cargo" class="form-select" required>
                                                        @foreach($cargos as $cargo)
                                                        <option value="{{ $cargo->id }}" {{ $usuario->usu_cargo == $cargo->id ? 'selected' : '' }}>
                                                            {{ $cargo->car_nombre }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="usu_punto" class="form-label">Punto de Trabajo</label>
                                                    <select name="usu_punto" class="form-select" required>
                                                        @foreach($puntos as $punto)
                                                        <option value="{{ $punto->id }}" {{ $usuario->usu_punto == $punto->id ? 'selected' : '' }}>
                                                            {{ $punto->nombre }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="password" class="form-label">Contraseña</label>
                                                    <input type="password" class="form-control" name="password">
                                                    <small class="text-muted">Dejar en blanco para no cambiar la contraseña</small>
                                                </div>    
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-2 text-center text-gray-500">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>
