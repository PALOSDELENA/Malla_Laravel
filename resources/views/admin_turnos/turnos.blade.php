<x-app-layout>
    <div class="container mt-5 mb-4">
        <h2 class="mb-4">Gestión de Tipos de Documetos</h2>

        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <button class="btn btn-sm btn-warning mb-4" data-bs-toggle="modal" data-bs-target="#crearTurno">
            NEW
        </button>

        <!-- Modal de Creación -->
        <div class="modal fade" id="crearTurno" tabindex="-1" aria-labelledby="crearTurno" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('turnos.store') }}" method="POST">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearTurno">Crear Turno</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="turno_nombre" class="form-label">Nombre del Turno</label>
                            <input type="text" class="form-control" id="turno_nombre" name="turno_nombre" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="turno_descripcion" class="form-label">Descripción del Turno</label>
                            <input type="text" class="form-control" id="turno_descripcion" name="turno_descripcion" required maxlength="255">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </div>
            </form>
        </div>
        </div>

        {{-- Tabla de cargos --}}
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Turno</th>
                    <th>Descripción</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($turnos as $turno)
                    <tr>
                        <td>{{ $turno->id_turnos }}</td>
                        <td>{{ $turno->tur_nombre }}</td>
                        <td>{{ $turno->tur_descripcion }}</td>
                        <td>
                            <!-- Botón Editar -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $turno->id_turnos }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Modal de Edición -->
                            <div class="modal fade" id="editModal{{ $turno->id_turnos }}" tabindex="-1" aria-labelledby="editModalLabel{{ $turno->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('turnos.update', $turno->id_turnos) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $turno->id_turnos }}">Editar Turno</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="turno_nombre_{{ $turno->id_turnos }}" class="form-label">Nombre del Turno</label>
                                                <input type="text" class="form-control" id="turno_{{ $turno->tur_nombre }}" name="turno_nombre" value="{{ $turno->tur_nombre }}" required maxlength="255">
                                            </div>
                                            <div class="mb-3">
                                                <label for="turno_descripcion_{{ $turno->id_turnos }}" class="form-label">Descripción del Turno</label>
                                                <input type="text" class="form-control" id="turno_descripcion_{{ $turno->tur_descripcion }}" name="turno_descripcion" value="{{ $turno->tur_descripcion }}" required maxlength="255">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $turno->id_turnos }})">
                                <i class="fas fa-trash"></i>
                            </button>

                            <!-- Formulario oculto -->
                            <form id="delete-form-{{ $turno->id_turnos }}" action="{{ route('turnos.destroy', $turno->id_turnos) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        {{ $turnos->links() }}
    </div>
</x-app-layout>
