<x-app-layout>
    <div class="container mt-4 mb-4">
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: "{{ session('success') }}",
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
        @endif
        <h2 class="mb-4">Listado de Productos</h2>

        <!-- Filtros -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="proNombre" class="form-control" placeholder="Buscar por nombre"
                       value="{{ request('proNombre') }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="proUnidadMedida" class="form-control" placeholder="Buscar por unidad de medida"
                       value="{{ request('proUnidadMedida') }}">
            </div>
            <div class="col-md-4 d-flex">
                <button type="submit" class="btn btn-warning me-2">Filtrar</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>

        <!-- Tabla -->
        <div class="table-responsive">
            <a href="{{ route('productos.create') }}" class="btn btn-warning mb-3">{{__('New')}}</a>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="table table-striped align-middle w-100 divide-y divide-gray-200 text-center text-gray-700">
                    <thead class="table-warning">
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Producto</th>
                            <th>Unidad de Medida</th>
                            <th>Tipo de Producto</th>
                            <th>Ingredientes</th>
                            <th>Condiciones de Conservación</th>
                            <th>Fabricante</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                            <tr>
                                <td>{{ $producto->id }}</td>
                                <td>{{ $producto->proNombre }}</td>
                                <td>{{ $producto->proUnidadMedida }}</td>
                                <td>{{ $producto->proTipo }}</td>
                                <td>{{ $producto->proListaIngredientes }}</td>
                                <td>{{ $producto->proCondicionesConservacion }}</td>
                                <td>{{ $producto->proFabricante }}</td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-warning btn-editar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarProductoModal"
                                        data-id="{{ $producto->id }}"
                                        data-nombre="{{ $producto->proNombre }}"
                                        data-unidad="{{ $producto->proUnidadMedida }}"
                                        data-tipo="{{ $producto->proTipo }}"
                                        data-ingredientes="{{ $producto->proListaIngredientes }}"
                                        data-condiciones="{{ $producto->proCondicionesConservacion }}"
                                        data-fabricante="{{ $producto->proFabricante }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-danger btn-eliminar"
                                        data-id="{{ $producto->id }}"
                                        data-nombre="{{ $producto->proNombre }}" data-url="{{ route('productos.destroy', ['producto' => '__ID__']) }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron productos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Formulario oculto para eliminar -->
            <form id="form-eliminar" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

            <!-- Modal de Edición de Producto -->
            <div class="modal fade" id="editarProductoModal" tabindex="-1" aria-labelledby="editarProductoLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" id="formEditarProducto">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarProductoLabel">Editar Producto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>

                            <div class="modal-body row g-3">
                                <input type="hidden" name="producto_id" id="producto_id">

                                <div class="col-md-6">
                                    <label for="proNombre" class="form-label">Nombre del Producto</label>
                                    <input type="text" name="proNombre" id="modal_proNombre" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="proUnidadMedida" class="form-label">Unidad de Medida</label>
                                    <select name="proUnidadMedida" id="modal_proUnidadMedida" class="form-select" required>
                                        <option value="">Seleccione una unidad</option>
                                        <option value="Kilogramo">Kilogramo</option>
                                        <option value="Gramo">Gramo</option>
                                        <option value="Litro">Litro</option>
                                        <option value="Unidad">Unidad</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="proTipo" class="form-label">Tipo de Producto</label>
                                    <select name="proTipo" id="modal_proTipo" class="form-select" required>
                                        <option value="">Seleccione el tipo</option>
                                        <option value="Materia Prima">Materia Prima</option>
                                        <option value="Producto Terminado">Producto Terminado</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="proFabricante" class="form-label">Fabricante</label>
                                    <input type="text" name="proFabricante" id="modal_proFabricante" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label for="proListaIngredientes" class="form-label">Ingredientes (separados por coma)</label>
                                    <input type="text" name="proListaIngredientes" id="modal_proListaIngredientes" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label for="proSeccion" class="form-label">Sección</label>
                                    <select name="proSeccion" id="modal_proSeccion" class="form-select">
                                        <option value="">Seleccione una unidad</option>
                                        <option value="1">Parrilla</option>
                                        <option value="2">Cocina</option>
                                        <option value="3">Bar</option>
                                        <option value="4">Almuerzos</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="proCondicionesConservacion" class="form-label">Condiciones de Conservación</label>
                                    <input type="text" name="proCondicionesConservacion" id="modal_proCondicionesConservacion" class="form-control">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-2">
                {{ $productos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
