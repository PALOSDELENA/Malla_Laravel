<x-app-layout>
    <div class="container mt-5 mb-4">
        <h2 class="mb-4">Gestión de Tipos de Documetos</h2>

        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Formulario para crear un nuevo cargo --}}
        <form action="{{ route('tipos-documentos.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <label for="tipo_documento" class="form-label">Nombre del Documeto</label>
                    <input type="text" name="tipo_documento" id="tipo_documento" class="form-control" required maxlength="255" value="{{ old('tipo_documento') }}">
                    @error('tipo_documento')
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
                    <th>Nombre del Documeto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tDocumentos as $documento)
                    <tr>
                        <td>{{ $documento->id }}</td>
                        <td>{{ $documento->tipo_documento }}</td>
                        <td>
                            {{-- Botones de acción aquí --}}
                            <!-- Botón Editar -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $documento->id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Modal de Edición -->
                            <div class="modal fade" id="editModal{{ $documento->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $documento->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('tipos-documentos.update', $documento->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $documento->id }}">Editar documento</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="tipo_documento_{{ $documento->id }}" class="form-label">Nombre del documento</label>
                                                <input type="text" class="form-control" id="tipo_documento_{{ $documento->id }}" name="tipo_documento" value="{{ $documento->tipo_documento }}" required maxlength="255">
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

                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $documento->id }})">
                                <i class="fas fa-trash"></i>
                            </button>

                            <!-- Formulario oculto -->
                            <form id="delete-form-{{ $documento->id }}" action="{{ route('tipos-documentos.destroy', $documento->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        {{ $tDocumentos->links() }}
    </div>
</x-app-layout>
