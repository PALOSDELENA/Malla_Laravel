<x-app-layout>
    <div class="container mt-5 mb-4">
        <h2 class="mb-4">Gestión de Puntos</h2>

        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Formulario para crear un nuevo cargo --}}
        <form action="{{ route('puntos.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre del Punto</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required maxlength="255" value="{{ old('nombre') }}">
                    @error('car_nombre')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning w-100">Guardar</button>
                </div>
            </div>
        </form>

        {{-- Tabla de cargos --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Punto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($puntos as $punto)
                    <tr>
                        <td>{{ $punto->id }}</td>
                        <td>{{ $punto->nombre }}</td>
                        <td>
                            {{-- Botones de acción aquí --}}
                            <!-- Botón Editar -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $punto->id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Modal de Edición -->
                            <div class="modal fade" id="editModal{{ $punto->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $punto->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('puntos.update', $punto->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $punto->id }}">Editar punto</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="nombre_{{ $punto->id }}" class="form-label">Nombre del punto</label>
                                                <input type="text" class="form-control" id="nombre_{{ $punto->id }}" name="nombre" value="{{ $punto->nombre }}" required maxlength="255">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $punto->id }})">
                                <i class="fas fa-trash"></i>
                            </button>

                            <!-- Formulario oculto -->
                            <form id="delete-form-{{ $punto->id }}" action="{{ route('puntos.destroy', $punto->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        {{ $puntos->links() }}
    </div>
</x-app-layout>
