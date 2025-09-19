<x-app-layout>
    @php
        $perfil = auth()->user()->cargo()->first()->car_nombre ?? 'Sin Cargo';
    @endphp
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="container mt-2">
    
    <!-- Filtros de fecha -->
    <form method="GET" class="filter-container">
        <div class="filter-group">
            <label>Desde:</label>
            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
        </div>
        <div class="filter-group">
            <label>Hasta:</label>
            <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
        </div>
        <div class="filter-buttons">
            <button type="submit" class="button">Filtrar</button>
            <a href="{{ route('ordenCompra') }}" class="button">Limpiar Filtros</a>
        </div>
    </form>

    <div class="actions-container">
        <a href="{{route('crearOrden')}}" class="button">Generar Nueva Orden</a>
    </div>

    @if (request('mensaje') === 'orden_eliminada')
        <div style="background-color: #4CAF50; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            La orden ha sido eliminada exitosamente.
        </div>
    @endif
    
    @if ($ordenes->isEmpty())
        <p class="no-results">No se encontraron órdenes para el período seleccionado.</p>
    @else
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Responsable</th>
                    <th>Correo Electrónico</th>
                    <th>Fecha Entrega 1</th>
                    <th>Fecha Entrega 2</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordenes as $orden)
                    <tr>
                        <td>{{ $orden->id }}</td>
                        <td>{{ $orden->responsable }}</td>
                        <td>{{ $orden->email }}</td>
                        <td>{{ $orden->fecha_entrega_1 }}</td>
                        <td>{{ $orden->fecha_entrega_2 }}</td>
                        <td>
                            <span class="estado-{{ $orden->estado }}">
                                {{ ucfirst($orden->estado ?? 'Pendiente') }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('ordenes.ver.pdf', $orden->id) }}" class="button">Ver</a>

                            @if ($perfil == 'Administrador')
                                @if ($orden->estado === 'Pendiente')
                                    <button type="button" 
                                            class="button button-edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#revisionModal"
                                            data-id="{{ $orden->id }}">
                                        Revisar Orden
                                    </button>
                                @endif
                                <a href="{{ route('editOrden', $orden->id) }}" class="button button-edit">
                                    <i class="fas fa-pen"></i> <!-- Icono de 'Editar' -->
                                </a>
                            @endif

                            @if ($perfil || $orden->estado === 'Pendiente')
                                <form action="{{ route('ordenes.destroy', $orden->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta orden?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <!-- Modal -->
            <div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- modal-lg para más espacio -->
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="revisionModalLabel">Revisar Orden de Compra #<?= $orden->id ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <div class="row">
                    <div class="col-md-6">
                        <p><strong>Responsable:</strong> <span id="ordenResponsable"></span></p>
                        <p><strong>Punto de Venta:</strong> <span id="ordenPunto"></span></p>
                        <p><strong>Correo:</strong> <span id="ordenCorreo"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha de Creación:</strong> <span id="ordenFechaCreacion"></span></p>
                        <p><strong>Fecha Entrega 1:</strong> <span id="ordenFecha1"></span></p>
                        <p><strong>Fecha Entrega 2:</strong> <span id="ordenFecha2"></span></p>
                    </div>
                    </div>

                    <form method="POST" action="{{ route('ordenes.revision', $orden->id) }}" class="mt-3">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Estado de la Orden:</label>
                            <select name="estado" class="form-select" required>
                                <option>Seleccionar</option>
                                <option value="aprobada">Aprobar Orden</option>
                                <option value="denegada">Denegar Orden</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Comentarios:</label>
                            <textarea name="comentario" class="form-control" required 
                                    placeholder="Ingrese sus comentarios sobre la orden..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('ordenCompra') }}" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Decisión</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
            </div>
        </table>
        
        <!-- Paginación -->
        {{ $ordenes->links() }}
    @endif
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var revisionModal = document.getElementById('revisionModal');
        revisionModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // botón que abrió el modal
            var ordenId = button.getAttribute('data-id'); // id de la orden

            console.log("Orden seleccionada:", ordenId);

            const baseUrl = "{{ url('/') }}";
            // Si quieres cargar los datos por AJAX
            fetch(`${baseUrl}/ordenes/${ordenId}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.error) {
                        document.getElementById('ordenResponsable').innerText = data.responsable;
                        document.getElementById('ordenPunto').innerText = data.punto_id;
                        document.getElementById('ordenCorreo').innerText = data.email;
                        document.getElementById('ordenFechaCreacion').innerText = new Date(data.created_at).toLocaleDateString();
                        document.getElementById('ordenFecha1').innerText = data.fecha_entrega_1;
                        document.getElementById('ordenFecha2').innerText = data.fecha_entrega_2;                    
                    }
                });
        });
    });
</script>
</x-app-layout>
