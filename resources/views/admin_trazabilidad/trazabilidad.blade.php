<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Trazabilidad de Productos') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 text-green-600 font-semibold">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->has('error'))
                <div class="alert alert-danger">
                    {{ $errors->first('error') }}
                </div>
            @endif
            @if ($errors->has('traCantidad'))
                <div class="alert alert-danger">
                    {{ $errors->first('traCantidad') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('trazabilidad.create') }}" class="btn btn-warning">
                    + Registrar Nuevo Movimiento
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="table w-100 divide-y divide-gray-200 text-sm text-center text-gray-700">
                    <thead class="table-warning">
                        <tr>
                            <th class="px-4 py-2">Fecha</th>
                            <th class="px-4 py-2">Tipo</th>
                            <th class="px-4 py-2">Producto</th>
                            <th class="px-4 py-2">Cantidad</th>
                            <th class="px-4 py-2">Lote/Serie</th>
                            <!-- <th class="px-4 py-2">Ubicación</th> -->
                            <th class="px-4 py-2">Responsable</th>
                            <!-- <th class="px-4 py-2">Color</th>
                            <th class="px-4 py-2">Textura</th>
                            <th class="px-4 py-2">Olor</th> -->
                            <th class="px-4 py-2">Observaciones</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($movimientos as $mov)
                            <tr>
                                <td class="px-4 py-2">{{ $mov->traFechaMovimiento }}</td>
                                <td class="px-4 py-2">{{ $mov->traTipoMovimiento }}</td>
                                <td class="px-4 py-2">{{ $mov->producto->proNombre ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $mov->traCantidad }}</td>
                                <td class="px-4 py-2">{{ $mov->traLoteSerie }}</td>
                                <!-- <td class="px-4 py-2">{{ $mov->traDestino }}</td> -->
                                <td class="px-4 py-2">{{ $mov->responsable->usu_nombre ?? '—' }}</td>
                                <!-- <td class="px-4 py-2">{{ $mov->traColor }}</td>
                                <td class="px-4 py-2">{{ $mov->traTextura }}</td>
                                <td class="px-4 py-2">{{ $mov->traOlor }}</td> -->
                                <td class="px-4 py-2">{{ $mov->traObservaciones }}</td>
                                <td class="px-4 py-2">
                                    <!-- Botón para abrir el modal -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $mov->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $mov->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- Formulario oculto -->
                                    <form id="delete-form-{{ $mov->id }}" action="{{ route('trazabilidad.destroy', $mov->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                <!-- Modal -->
                                <div class="modal fade" id="editModal{{ $mov->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $mov->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('trazabilidad.update', $mov->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Trazabilidad</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Fecha</label>
                                                        <input type="date" name="traFechaMovimiento" class="form-control" value="{{ $mov->traFechaMovimiento }}" disabled>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Tipo de Movimiento</label>
                                                        <select name="traTipoMovimiento" class="form-select" disabled>
                                                            @foreach(['Ingreso', 'Egreso', 'Devolución'] as $tipo)
                                                                <option value="{{ $tipo }}" @selected($mov->traTipoMovimiento == $tipo)>{{ $tipo }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Producto</label>
                                                        <select name="traIdProducto" class="form-select" disabled>
                                                            @foreach($productos as $producto)
                                                                <option value="{{ $producto->id }}" @selected($mov->traIdProducto == $producto->id)>
                                                                    {{ $producto->proNombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Cantidad</label>
                                                        <input type="number" name="traCantidad" class="form-control" step="0.01" value="{{ $mov->traCantidad }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Lote/Serie</label>
                                                        <input type="text" name="traLoteSerie" class="form-control" value="{{ $mov->traLoteSerie }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Ubicación</label>
                                                        <select name="traDestino" class="form-select" required>
                                                            <option value="Puente Aranda" @selected($mov->traDestino == 'Puente Aranda')>Puente Aranda</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Responsable</label>
                                                        <select name="traResponsable" class="form-select" required>
                                                            @foreach($usuarios as $user)
                                                                <option value="{{ $user->num_doc }}" @selected($mov->traResponsable == $user->num_doc)>
                                                                    {{ $user->usu_nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Color</label>
                                                        <select name="traColor" class="form-select" required>
                                                            <option value="Bueno" @selected($mov->traColor == 'Bueno')>Bueno</option>
                                                            <option value="Malo" @selected($mov->traColor == 'Malo')>Malo</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Textura</label>
                                                        <select name="traTextura" class="form-select" required>
                                                            <option value="Bueno" @selected($mov->traTextura == 'Bueno')>Bueno</option>
                                                            <option value="Malo" @selected($mov->traTextura == 'Malo')>Malo</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Olor</label>
                                                        <select name="traOlor" class="form-select" required>
                                                            <option value="Bueno" @selected($mov->traOlor == 'Bueno')>Bueno</option>
                                                            <option value="Malo" @selected($mov->traOlor == 'Malo')>Malo</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label">Observaciones</label>
                                                        <textarea name="traObservaciones" class="form-control" rows="2">{{ $mov->traObservaciones }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                                                    <button class="btn btn-warning" type="submit">Guardar Cambios</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-2 text-center text-gray-500">No hay movimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-2">
                {{ $movimientos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
