<x-app-layout>
    <div class="container mt-5 mb-4">
        <h2 class="mb-4">Gestión de Producciones</h2>
        <a href="{{ route('producciones.create') }}" class="btn btn-info"><i class="fa-solid fa-plus"></i></a>

        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Tabla de cargos --}}
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($producciones as $produccion)
                    <tr>
                        <td>{{ $produccion->id }}</td>
                        <td>{{ $produccion->produccion }}</td>
                        <td>
                            {{-- Botones de acción aquí --}}
                            <!-- Botón Editar -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $produccion->id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Modal de Edición -->
                            <div class="modal fade" id="editModal{{ $produccion->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $produccion->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('producciones.update', $produccion->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $produccion->id }}">Editar produccion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="produccion_{{ $produccion->id }}" class="form-label">Nombre del produccion</label>
                                                <input type="text" class="form-control" id="produccion_{{ $produccion->id }}" name="produccion" value="{{ $produccion->produccion }}" required maxlength="255">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Materias Primas Asociadas</label>

                                                <div id="materiasPrimasContainer{{ $produccion->id }}">
                                                    @foreach ($produccion->productos as $index => $producto)
                                                        <div class="input-group mb-2 materia-select">
                                                            <select name="materias_primas[]" class="form-select" required>
                                                                <option value="">Seleccione una materia prima</option>
                                                                @foreach ($materiasPrimas as $materiaPrima)
                                                                    <option value="{{ $materiaPrima->id }}"
                                                                        @if ($materiaPrima->id === $producto->id) selected @endif>
                                                                        {{ $materiaPrima->proNombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-danger removeSelect"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button type="button" class="btn btn-success mt-2" id="addMateriaPrima">
                                                    <i class="fas fa-plus"></i> Agregar Materia Prima
                                                </button>
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

                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $produccion->id }})">
                                <i class="fas fa-trash"></i>
                            </button>

                            <!-- Formulario oculto -->
                            <form id="delete-form-{{ $produccion->id }}" action="{{ route('producciones.destroy', $produccion->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        {{ $producciones->links() }}
    </div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const materias = @json($materiasPrimas);

        document.querySelectorAll('.addMateriaPrimaBtn').forEach(button => {
            button.addEventListener('click', function () {
                const prodId = this.dataset.target;
                const container = document.getElementById('materiasPrimasContainer' + prodId);

                if (!container) return;

                const div = document.createElement('div');
                div.classList.add('input-group', 'mb-2', 'materia-select');

                let selectHTML = '<select name="materias_primas[]" class="form-select" required>';
                selectHTML += '<option value="">Seleccione una materia prima</option>';
                materias.forEach(m => {
                    selectHTML += `<option value="${m.id}">${m.proNombre}</option>`;
                });
                selectHTML += '</select>';

                div.innerHTML = selectHTML + `<button type="button" class="btn btn-danger removeSelect"><i class="fas fa-trash"></i></button>`;
                container.appendChild(div);
            });
        });

        // Delegación global para eliminar
        document.body.addEventListener('click', function (e) {
            if (e.target.closest('.removeSelect')) {
                e.target.closest('.materia-select')?.remove();
            }
        });
    });
</script>
</x-app-layout>
