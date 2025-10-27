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
                <!-- Filtros a la izquierda -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <form method="GET" action="{{ route('novedad.index') }}" class="flex items-center gap-4">
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
                    <a href="{{ route('novedad.index') }}" class="btn btn-secondary btn-sm">
                        Limpiar
                    </a>
                </div>

                <!-- Botón de registrar a la derecha -->
                <div>
                    <button type="button" class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovedad">
                        + Registrar Novedad
                    </button>
                </div>

                <!-- Botón para abrir modal -->
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalExportar">
                    <i class="fa-solid fa-file-excel"></i>
                </button>

                <!-- Modal -->
                <div class="modal fade" id="modalExportar" tabindex="-1" aria-labelledby="modalExportarLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <form id="formExport" action="{{ route('novedad.exportar') }}" method="GET">
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

            <!-- Modal Crear Novedad -->
            <div class="modal fade" id="modalNovedad" tabindex="-1" aria-labelledby="modalNovedadLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title fw-bold" id="modalNovedadLabel">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Registrar Novedad
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <form id="formNovedad" action="{{ route('novedad.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="id_producto" class="form-label fw-semibold">Insumo</label>
                                    <select name="id_producto" id="id_producto" class="form-select" required>
                                    <option value="">Seleccione un insumo...</option>
                                    @foreach ($insumos as $insumo)
                                        <option value="{{ $insumo->id }}">{{ $insumo->proNombre }}</option>
                                    @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="comentario_operario" class="form-label fw-semibold">Comentario del operario</label>
                                    <textarea name="comentario_operario" id="comentario_operario" class="form-control" rows="3" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="fecha_novedad" class="form-label fw-semibold">Fecha de la novedad</label>
                                    <input type="date" name="fecha_novedad" id="fecha_novedad" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Imágenes (máximo 5)</label>
                                    <input type="file" name="imagenes[]" id="imagenes" class="form-control" accept="image/*" multiple required>
                                    <small class="text-muted">Puede seleccionar hasta 5 imágenes (JPG, PNG, GIF).</small>
                                </div>

                                <!-- Previsualización -->
                                <div id="preview" class="d-flex flex-wrap gap-2 mt-3"></div>
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
                <div id="loading" class="hidden fixed inset-0 flex items-center justify-center bg-white/80 z-50">
                    <div class="animate-spin rounded-full h-10 w-10 border-t-4 border-orange-500"></div>
                </div>
                <table class="table w-100 divide-y divide-gray-200 text-sm text-center text-gray-700">
                    <thead class="table-warning">
                        <tr>
                            <th class="px-4 py-2">Insumo</th>
                            <th class="px-4 py-2">Comentario Operario</th>
                            <th class="px-4 py-2">Comentario Admin</th>
                            <th class="px-4 py-2">Fecha Novedad</th>
                            <th class="px-4 py-2">Estado</th>
                            @if($punto == 3 || $punto == 17)
                                <th class="px-4 py-2">Punto</th>
                            @endif
                            <th class="px-4 py-2">Imagenes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($novedades as $nov)
                            <tr class="clickable-row"
                                data-id="{{ $nov->id }}"
                                data-insumo="{{ $nov->producto->proNombre ?? 'N/A' }}"
                                data-comentario="{{ $nov->comentario_operario ?? 'Sin comentario' }}"
                                data-punto="{{ $nov->punto->nombre }}"
                                data-imagenes='@json($nov->imagenes)'
                                style="cursor: pointer;">
                                <td class="px-4 py-2">{{ $nov->producto->proNombre }}</td>
                                <td class="px-4 py-2">{{ $nov->comentario_operario }}</td>
                                <td class="px-4 py-2">{{ $nov->comentario_admin ?? '—' }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($nov->fecha_novedad)->format('d-m-Y') }}</td>
                                <td class="px-4 py-2">{{ $nov->estado }}</td>
                                @if($punto == 3 || $punto == 17)
                                    <td class="px-4 py-2">{{ $nov->punto->nombre }}</td>
                                @endif
                                <td class="px-4 py-2">
                                    @php
                                        // Normalizar imágenes (pueden venir como JSON o coma separada)
                                        $imagenes = [];
                                        if ($nov->imagenes) {
                                            $imagenes = is_array($nov->imagenes)
                                                ? $nov->imagenes
                                                : json_decode($nov->imagenes, true);

                                            if (!is_array($imagenes)) {
                                                $imagenes = explode(',', $nov->imagenes);
                                            }
                                        }
                                    @endphp

                                    @if (!empty($imagenes))
                                        <!-- Icono que abre el visor -->
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#visorModal{{ $nov->id }}"  onclick="event.stopPropagation()">
                                            <i class="fa-solid fa-image text-warning fs-5 mx-1"></i>
                                        </a>

                                        <!-- Modal con carrusel -->
                                        <div class="modal fade" id="visorModal{{ $nov->id }}" tabindex="-1" aria-labelledby="visorModalLabel{{ $nov->id }}" aria-hidden="true" onclick="event.stopPropagation()">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content bg-dark text-white">
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-title" id="visorModalLabel{{ $nov->id }}">Imágenes de novedad</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>
                                                    <div class="modal-body p-0">
                                                        <div id="carousel{{ $nov->id }}" class="carousel slide" data-bs-ride="carousel">
                                                            <div class="carousel-inner">
                                                                @foreach ($imagenes as $index => $img)
                                                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                                        <img src="{{ Storage::url($img) }}" class="d-block w-100 rounded" alt="Imagen {{ $index + 1 }}">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $nov->id }}" data-bs-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Anterior</span>
                                                            </button>
                                                            <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $nov->id }}" data-bs-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Siguiente</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-2 text-center text-gray-500">No hay novedades registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Modal Agregar Comentario -->
                <div class="modal fade" id="modalComentario" tabindex="-1" aria-labelledby="modalComentarioLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-dark">
                                <h5 class="modal-title fw-bold" id="modalComentarioLabel">
                                    <i class="fa-solid fa-comments me-2"></i> Agregar Comentario
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>

                            <form id="formComentario" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">

                                    <input type="hidden" name="id_novedad" id="id_novedad">

                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Insumo</label>
                                            <input type="text" id="insumo_nombre" class="form-control" readonly>
                                        </div>

                                        <div class="col-sm-12 col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Punto</label>
                                            <input type="text" id="punto" class="form-control" readonly>
                                        </div>

                                        <div class="col-sm-12 col-md-12 mb-3">
                                            <label class="form-label fw-semibold">Comentario del Operario</label>
                                            <textarea id="com_operario" class="form-control" rows="3" readonly></textarea>
                                        </div>

                                        <div class="col-sm-12 col-md-12 mb-3">
                                            <label class="form-label fw-semibold">Comentario del Administrador</label>
                                            <textarea name="comentario_admin" id="comentario_admin" class="form-control" rows="3" required></textarea>
                                        </div>

                                        <!-- Previsualización -->
                                        <div id="preview-2" class="col-sm-12 col-md-12 mb-3"></div>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-info fw-bold">Guardar Comentario</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-2">
                {{ $novedades->links() }}
            </div>
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
    const inputImagenes = document.getElementById('imagenes');
    const preview = document.getElementById('preview');

    inputImagenes.addEventListener('change', function(event) {
        const files = Array.from(inputImagenes.files);

        // Limitar a máximo 5
        if (files.length > 5) {
            // SweetAlert en vez de alert()
            Swal.fire({
                icon: 'warning',
                title: '¡Demasiadas imágenes!',
                text: 'Solo puede seleccionar hasta 5 imágenes.',
                confirmButtonColor: '#f59e0b' // color similar a tu boton warning
            });

            // Reiniciar input y preview
            inputImagenes.value = '';
            preview.innerHTML = '';
            return;
        }

        // Limpiar previsualización anterior
        preview.innerHTML = '';

        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('rounded', 'shadow-sm');
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>

<script>
    const form = document.getElementById('form-novedad');
    const loading = document.getElementById('loading');

    form.addEventListener('submit', () => {
    loading.classList.remove('hidden'); // Mostrar spinner
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
                const comentario = row.dataset.comentario;
                const imagenes = JSON.parse(row.dataset.imagenes || '[]');

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

        // Detecta envío de este formulario específico
        const formNovedad = document.getElementById('formNovedad');
        formNovedad.addEventListener('submit', () => {
            overlay.style.display = 'flex';
        });

        // Detecta envío de este formulario específico
        const formComentario = document.getElementById('formComentario');
        formComentario.addEventListener('submit', () => {
            overlay.style.display = 'flex';
        });

        // Por seguridad, oculta el overlay si se cancela o hay error
        window.addEventListener('pageshow', () => {
            overlay.style.display = 'none';
        });
    });
</script>
