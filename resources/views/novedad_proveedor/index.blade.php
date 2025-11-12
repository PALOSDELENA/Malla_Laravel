<x-app-layout>
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

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ups!</strong> Hay algunos errores en tu formulario.<br><br>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4 d-flex justify-content-between flex-wrap align-items-center gap-3">
                <!-- Filtro - Fecha -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <form id="formFiltroFechas" method="GET" action="{{ route('novedades.filtrar') }}" class="flex items-center gap-4">
                        <div class="d-flex align-items-center">
                            <label for="fecha_inicio" class="me-2 mb-0 fw-semibold" style="width: 110px;">Fecha inicio:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm" style="width: 180px;">
                        </div>

                        <div class="d-flex align-items-center">
                            <label for="fecha_fin" class="me-2 mb-0 fw-semibold" style="width: 110px;">Fecha fin:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-sm" style="width: 180px;">
                        </div>

                        <button type="submit" id="filtrarFechas" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-filter me-1"></i> Filtrar
                        </button>
                    </form>
                    <a href="{{ route('nov.proveedor.index') }}" class="btn btn-secondary btn-sm">
                        Limpiar
                    </a>
                </div>

                <div>
                    <button type="button" class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovedadProveedor">
                        + Registrar Novedad
                    </button>
                </div>

                <!-- Botón para abrir modal -->
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalExportar">
                    <i class="fa-solid fa-file-excel"></i>
                </button>

                <!-- Modal - Exportar -->
                <div class="modal fade" id="modalExportar" tabindex="-1" aria-labelledby="modalExportarLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <form id="formExport" action="{{ route('novedades.exportarExcel') }}" method="GET">
                            <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="modalExportarLabel">Exportar Novedades</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Desde:</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Hasta:</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                            </div>
                            </div>

                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Generar Excel</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Crear Novedad de Proveedor -->
            <div class="modal fade" id="modalNovedadProveedor" tabindex="-1" aria-labelledby="modalNovedadProveedorLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title fw-bold" id="modalNovedadProveedorLabel">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> Registrar Novedad de Proveedor
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <form id="formNovedadProveedor" action="{{ route('nov.proveedor.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">

                                <!-- Seleccionar Proveedor -->
                                <div class="mb-3">
                                    <label for="id_proveedor" class="form-label fw-semibold">Proveedor</label>
                                    <select name="id_proveedor" id="id_proveedor" class="form-select select-bold-options" required>
                                        <option value="">Seleccione un proveedor...</option>
                                        @foreach ($novedades as $prov)
                                            <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Insumo (productos del proveedor) -->
                                <div class="mb-3">
                                    <label for="id_producto" class="form-label fw-semibold">Insumo</label>
                                    <select name="id_producto" id="id_producto" class="form-select select-bold-options" required>
                                        <option value="">Seleccione un insumo...</option>
                                    </select>
                                </div>

                                <!-- Campos de la tabla pivote -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="calidad_producto" class="form-label fw-semibold">Calidad del Producto</label>
                                        <select name="calidad_producto" id="calidad_producto" class="form-control" required>
                                            <option value="excelente">Excelente</option>
                                            <option value="aceptable">Aceptable</option>
                                            <option value="bueno">Bueno</option>
                                            <option value="malo">Malo</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="tiempo_entrega" class="form-label fw-semibold">Tiempo de Entrega</label>
                                        <select name="tiempo_entrega" id="tiempo_entrega" class="form-control" required>
                                            <option value="excelente">Excelente</option>
                                            <option value="aceptable">Aceptable</option>
                                            <option value="bueno">Bueno</option>
                                            <option value="malo">Malo</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="presentacion_personal" class="form-label fw-semibold">Presentación Personal</label>
                                        <select name="presentacion_personal" id="presentacion_personal" class="form-control" required>
                                            <option value="excelente">Excelente</option>
                                            <option value="aceptable">Aceptable</option>
                                            <option value="bueno">Bueno</option>
                                            <option value="malo">Malo</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="observacion" class="form-label fw-semibold">Observación</label>
                                        <input type="text" name="observacion" id="observacion" class="form-control" placeholder="Observaciones adicionales...">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning fw-bold">Guardar Novedad</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <!-- Abvertencia en caso de no existir registros a importar -->
                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <style>
                    /* Ajustes para que los selects en los th mantengan la misma apariencia de la cabecera */
                    .table thead th { vertical-align: middle; }
                    .table thead th .form-select {
                        margin: 0 !important;
                        padding: .25rem .5rem;
                        height: calc(1.8rem + 2px);
                        background: transparent;
                        border: 0;
                        font-weight: 700;
                        box-shadow: none;
                        text-align: center;
                        -webkit-appearance: none;
                        -moz-appearance: none;
                        appearance: none;
                        /* Flecha personalizada como background (SVG inline) para el desplegable */
                        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'><path fill='%23000' d='M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.06z'/></svg>");
                        background-repeat: no-repeat;
                        background-position: right .5rem center;
                        background-size: .9rem;
                        padding-right: 1.8rem; /* espacio para la flecha */
                    }
                    .table thead th .form-select:focus { box-shadow: none; outline: none; }
                    /* Opciones en negrita para selects específicos */
                    .select-bold-options option { font-weight: 700 !important; }
                </style>

                <table class="table w-100 divide-y divide-gray-200 text-sm text-center text-gray-700">
                    <thead class="table-warning">
                        <tr>
                            <th class="align-middle">           
                                <select id="filtroProveedor" class="form-select form-select-sm bg-transparent text-center select-bold-options" onchange="filtrarTabla()">
                                    <option value="">Proveedor <i class="fa-solid fa-chevron-down"></i></option>
                                    @foreach ($novedades as $nov)
                                        <option value="{{ $nov->nombre }}">{{ $nov->nombre }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th>               
                                <select id="filtroProducto" class="form-select form-select-sm bg-transparent text-center select-bold-options" onchange="filtrarTabla()">
                                    <option value="">Producto <i class="fa-solid fa-chevron-down"></i></option>
                                    @foreach ($insumos as $insumo)
                                        <option value="{{ $insumo->proNombre }}">{{ $insumo->proNombre }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="px-4 py-2">Calidad Producto</th>
                            <th class="px-4 py-2">Tiempos de Entrega</th>
                            <th class="px-4 py-2">Presentación Personal</th>
                            <th class="px-4 py-2">Observaciones</th>
                            <th class="px-4 py-2">Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyNovedades" class="divide-y divide-gray-100">
                        @forelse ($novedades as $nov)
                            @foreach ($nov->productosNovedad as $producto)
                                <tr class="clickable-row" style="cursor: pointer;"
                                    data-id="{{ $nov->id }}">
                                    <td class="px-4 py-2">{{ $nov->nombre }}</td>
                                    <td class="px-4 py-2">{{ $producto->proNombre }}</td>
                                    <td class="px-4 py-2">{{ $producto->pivot->calidad_producto }}</td>
                                    <td class="px-4 py-2">{{ $producto->pivot->tiempo_entrega }}</td>
                                    <td class="px-4 py-2">{{ $producto->pivot->presentacion_personal }}</td>
                                    <td class="px-4 py-2">{{ $producto->pivot->observacion }}</td>
                                    <td class="px-4 py-2">{{ $producto->pivot->created_at ? $producto->pivot->created_at->format('d-m-Y') : '—' }}</td>
                                </tr>
                            @endforeach
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-2 text-center text-gray-500">No hay novedades registradas.</td>
                            </tr>
                        @endforelse
                            <th class="align-middle">           

            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-2">
                {{ $novedades->links() }}
            </div>
    </div>

    <!-- Overlay de carga -->
    <div id="loading-overlay">
    <div class="spinner-border text-warning" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const proveedorSelect = document.getElementById('id_proveedor');
    const productoSelect = document.getElementById('id_producto');

    proveedorSelect.addEventListener('change', function() {
        const proveedorId = this.value;

        productoSelect.innerHTML = '<option value="">Cargando...</option>';

        if (proveedorId) {
            const baseUrl = "{{ url('/') }}";
            fetch(`${baseUrl}/novedad/productos/${proveedorId}`)
                .then(response => response.json())
                .then(data => {
                    productoSelect.innerHTML = '<option value="">Seleccione un insumo...</option>';
                    data.forEach(producto => {
                        productoSelect.innerHTML += `<option value="${producto.id}">${producto.proNombre}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar productos:', error);
                    productoSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        } else {
            productoSelect.innerHTML = '<option value="">Seleccione un proveedor primero...</option>';
        }
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formComentario');
        const rows = document.querySelectorAll('.clickable-row');
        const modal = new bootstrap.Modal(document.getElementById('modalComentario'));
        const previewContainer = document.getElementById('preview-2');

        rows.forEach(row => {
            row.addEventListener('click', (event) => {
                // Evita conflicto si el clic fue en un icono de imagen
                if (event.target.closest('a, i')) return;

                // Obtener datos de la fila
                const id = row.dataset.id;
                const insumo = row.dataset.insumo;
                const punto = row.dataset.punto;
                const puntoUser = row.dataset.puntoUser;
                console.log(puntoUser);
                const comentario = row.dataset.comentario;
                const imagenes = JSON.parse(row.dataset.imagenes || '[]');

                // Solo abrir modal si el punto del usuario es 3 o 17
                if (!['Administrativo', 'Planta'].includes(puntoUser)) {
                    console.log('Usuario sin permiso para abrir modal');
                    return;
                }

                // Asignar al modal
                document.getElementById('id_novedad').value = id;
                document.getElementById('insumo_nombre').value = insumo;
                document.getElementById('punto').value = punto;
                document.getElementById('com_operario').value = comentario;

                // Limpiar previsualización anterior
                previewContainer.innerHTML = '';

                // Crear previsualización de imágenes
                imagenes.forEach(img => {
                    const imgElement = document.createElement('img');
                    imgElement.src = `/storage/${img}`;
                    imgElement.alt = "Imagen de novedad";
                    imgElement.classList.add('rounded', 'border', 'p-1');
                    imgElement.style.width = '100px';
                    imgElement.style.height = '100px';
                    imgElement.style.objectFit = 'cover';
                    previewContainer.appendChild(imgElement);
                });

                // Actualizar acción del formulario
                form.action = `/novedades/update/${id}`;

                // Mostrar modal
                modal.show();
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('loading-overlay');

        // Helper to show overlay and disable submit button to avoid double submits
        function showLoadingForForm(form) {
            if (!overlay || !form) return;
            overlay.style.display = 'flex';
            const submit = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submit) submit.disabled = true;
        }

        // Formulario principal de crear novedad (id: formNovedadProveedor)
        const formNovedadProv = document.getElementById('formNovedadProveedor');
        if (formNovedadProv) {
            formNovedadProv.addEventListener('submit', () => showLoadingForForm(formNovedadProv));
        }

        // Formulario de comentario/edición (si existe)
        const formComentario = document.getElementById('formComentario');
        if (formComentario) {
            formComentario.addEventListener('submit', () => showLoadingForForm(formComentario));
        }

        // Formulario de filtro por fecha (GET) - mostrar overlay al enviar
        const formFiltroFechas = document.getElementById('formFiltroFechas');
        if (formFiltroFechas) {
            formFiltroFechas.addEventListener('submit', () => showLoadingForForm(formFiltroFechas));
        }

        // Por seguridad, oculta el overlay si se vuelve a la página desde cache/back
        window.addEventListener('pageshow', () => {
            if (overlay) overlay.style.display = 'none';
            // Re-enable any submit buttons just in case
            [formNovedadProv, formComentario, formFiltroFechas].forEach(f => {
                if (!f) return;
                const submit = f.querySelector('button[type="submit"], input[type="submit"]');
                if (submit) submit.disabled = false;
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('tbodyNovedades');

    // Exponer la función en window para que pueda llamarse desde onchange="filtrarTabla()"
    window.filtrarTabla = async function() {
        const filtroProveedor = document.getElementById('filtroProveedor');
        const filtroProducto = document.getElementById('filtroProducto');

        const proveedor = filtroProveedor ? filtroProveedor.value : '';
        const producto = filtroProducto ? filtroProducto.value : '';

        // Construir la URL con parámetros correctamente
        const baseUrl = "{{ route('novedades.filtrarFetch') }}";
        const params = new URLSearchParams();

        if (proveedor) params.append('proveedor', proveedor);
        if (producto) params.append('producto', producto);

        try {
            const response = await fetch(`${baseUrl}?${params.toString()}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();

            // Limpiar la tabla
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-2">
                            No hay registros que coincidan con los filtros seleccionados.
                        </td>
                    </tr>
                `;
                return;
            }

            // Rellenar con los resultados
            data.forEach(nov => {
                nov.productos.forEach(prod => {
                    const fila = `
                        <tr>
                            <td>${nov.proveedor}</td>
                            <td>${prod.producto}</td>
                            <td>${prod.calidad_producto}</td>
                            <td>${prod.tiempo_entrega}</td>
                            <td>${prod.presentacion_personal}</td>
                            <td>${prod.observacion ?? ''}</td>
                            <td>${prod.fecha ?? ''}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', fila);
                });
            });
        } catch (err) {
            console.error('Error al filtrar novedades:', err);
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger py-2">
                        Ocurrió un error al cargar los datos. Revisa la consola.
                    </td>
                </tr>
            `;
        }
    }

    // También mantener listeners por si prefieres no usar attributes inline
    const filtroProveedor = document.getElementById('filtroProveedor');
    const filtroProducto = document.getElementById('filtroProducto');

    if (filtroProveedor) filtroProveedor.addEventListener('change', window.filtrarTabla);
    if (filtroProducto) filtroProducto.addEventListener('change', window.filtrarTabla);
});
</script>
