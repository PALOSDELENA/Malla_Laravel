<x-app-layout>
    <div class="container py-6 px-4">
        <a href="{{ route('ordenProduccion.create') }}" class="btn btn-warning mb-4">Nueva Orden</a>

        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
            <table class="table w-100 divide-y divide-gray-200 text-center text-gray-700">
                <thead class="table-warning">
                <tr>
                    <th>ID</th>
                    <th>Responsable</th>
                    <th>Producción</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($ordenes as $orden)
                        <tr>
                            <td>{{ $orden->id }}</td>
                            <td>{{ $orden->responsable1->usu_nombre ?? 'N/A' }}</td>
                            <td>{{ $orden->producciones->produccion ?? 'N/A' }}</td>
                            <td>{{ $orden->cantidad }}</td>
                            <td>{{ $orden->estado }}</td>
                            <td>{{ $orden->fecha_inicio }}</td>
                            <td>{{ $orden->fecha_fin ?? '-' }}</td>
                            <td>
                                <!-- Botón que abre el modal -->
                                <button 
                                    type="button" 
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $orden->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $orden->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Formulario oculto para eliminar -->
                                <form id="delete-form-{{ $orden->id }}" action="{{ route('ordenProduccion.destroy', $orden->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>

                        <!-- Modal de edición -->
                        <div class="modal fade" id="editModal{{ $orden->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $orden->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('ordenProduccion.update', $orden->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $orden->id }}">Editar Orden #{{ $orden->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="responsable">Responsable</label>
                                                    <select name="responsable" class="form-select" required>
                                                        @foreach($responsables as $num_doc => $nombre)
                                                            <option value="{{ $num_doc }}" @selected($orden->responsable1->num_doc == $num_doc)>{{ $nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Cantidad</label>
                                                    <input type="number" name="cantidad" class="form-control" value="{{ $orden->cantidad }}" required>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    h
                                                        <div id="materias-container-{{ $orden->id }}" class="row">
                                                            <!-- Aquí se cargan las materias primas consumidas -->
                                                        </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Estado</label>
                                                    <select name="estado" class="form-select" required>
                                                        @foreach(['Pendiente','En Proceso','Finalizado'] as $estado)
                                                            <option value="{{ $estado }}" @selected($orden->estado == $estado)>{{ $estado }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Fecha Inicio</label>
                                                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $orden->fecha_inicio }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Fecha Fin</label>
                                                    <input type="date" name="fecha_fin" class="form-control" value="{{ $orden->fecha_fin }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Novedad</label>
                                                    <textarea name="novedadProduccion" class="form-control">{{ $orden->novedadProduccion }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-2">
            {{ $ordenes->links() }}
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modals = document.querySelectorAll('.modal');

        modals.forEach(modal => {
            modal.addEventListener('shown.bs.modal', function (event) {
                const ordenId = this.id.replace('editModal', '');
                const container = document.getElementById(`materias-container-${ordenId}`);
                if (!container) return;

                container.innerHTML = '<div class="text-muted">Cargando...</div>';

                fetch(`/ordenProduccion/${ordenId}/materias-primas-consumo`)
                    .then(response => response.json())
                    .then(data => {
                        container.innerHTML = '';
                        if (data.length === 0) {
                            container.innerHTML = '<div class="text-danger">No hay materias primas registradas.</div>';
                        } else {
                            data.forEach(materia => {
                                container.innerHTML += `
                                    <div class="col-md-6 mb-2 p-2 rounded bg-light">
                                        <label>${materia.nombre} - ${materia.unidad ?? ''}</label>
                                        <input type="number" name="proCantidad" class="form-control" value="${materia.cantidad}" required>
                                    </div>
                                `;
                            });
                        }
                    })
                    .catch(error => {
                        container.innerHTML = '<div class="text-danger">Error al cargar las materias primas.</div>';
                        console.error(error);
                    });
            });
        });
    });
</script>
</x-app-layout>
