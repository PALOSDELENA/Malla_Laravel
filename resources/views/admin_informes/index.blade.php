<x-app-layout>
    <div class="container mt-4 mb-4">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            @php
                $cards = [
                    ['title' => 'Compra de Insumos', 'icon' => 'fa-store'],
                    ['title' => 'Consumo de Insumos', 'icon' => 'fa-box-open'],
                    ['title' => 'Encuesta', 'icon' => 'fa-clipboard-list'],
                    ['title' => 'Facturas Proveedor', 'icon' => 'fa-file-invoice-dollar'],
                    ['title' => 'Facturas Servicios', 'icon' => 'fa-file-invoice'],
                    ['title' => 'Recetas', 'icon' => 'fa-utensils'],
                ];
            @endphp

            @foreach($cards as $card)
                <div class="col-md-3 mb-4">
                    <div class="card text-center open-modal" data-title="{{ $card['title'] }}">
                        <div class="mt-4 mb-2">
                            <i class="fa-solid {{ $card['icon'] }} fa-5x icon-hover"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $card['title'] }}</h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="filterForm" method="GET" action="{{ route('exportar.insumos') }}">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="filterModalLabel">Filtrar por fechas</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="modal-body-content">
                        <input type="hidden" name="categoria" id="categoria">

                        {{-- 🔹 Contenido por defecto (fechas) --}}
                        <div class="mb-3 filtro-fechas">
                            <label for="fecha_inicio" class="form-label">Fecha inicial:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
                        </div>

                        <div class="mb-3 filtro-fechas">
                            <label for="fecha_fin" class="form-label">Fecha final:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                        </div>

                        {{-- 🔹 Contenido alternativo (para recetas) --}}
                        <div class="mb-3 filtro-punto d-none">
                            <label for="punto" class="form-label">Seleccione el punto:</label>
                            <select id="punto" name="punto" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="PUENTE ARANDA">Puente Aranda</option>
                                <option value="QUINTA PAREDES">Quinta Paredes</option>
                                <option value="MALL">Mall Palza</option>
                                <option value="CENTRO">Centro</option>
                                <option value="JIMENEZ">Jimenez</option>
                                <option value="MULTI">Multi Plaza</option>
                                <option value="SALITRE PLAZA">Salitre Plaza</option>
                                <option value="CAFAM FLORESTA">Cafam Floresta</option>
                                <option value="NUESTRO BOGOTA">Nuestro Bogotá</option>
                                <option value="FONTIBON">Fontibon</option>
                                <option value="HAYUELOS">Hayuelos</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Exportar Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = new bootstrap.Modal(document.getElementById('filterModal'));
            const categoriaInput = document.getElementById('categoria');
            const modalTitle = document.getElementById('filterModalLabel');
            const form = document.getElementById('filterForm');

            const filtrosFecha = document.querySelectorAll('.filtro-fechas');
            const filtroPunto = document.querySelector('.filtro-punto');

            // 🔹 Definir rutas por título
            const rutas = {
                'Compra de Insumos': "{{ route('exportar.insumos') }}",
                'Consumo de Insumos': "{{ route('exportar.consumo') }}",
                'Facturas Proveedor': "{{ route('exportar.facturas.proveedor') }}",
                'Facturas Servicios': "{{ route('exportar.facturas.servicios') }}",
                'Recetas': "{{ route('exportar.recetas') }}",
                'Encuesta': "{{ route('exportar.encuesta') }}"
            };

            // 🔹 Evento para cada card
            document.querySelectorAll('.open-modal').forEach(card => {
                card.addEventListener('click', function() {
                    const title = this.getAttribute('data-title');
                    modalTitle.textContent = `Filtrar ${title}`;
                    categoriaInput.value = title;
                    form.action = rutas[title] ?? "{{ route('exportar.insumos') }}";

                    // Mostrar/ocultar según la categoría
                    if (title === 'Recetas') {
                        filtrosFecha.forEach(f => f.classList.add('d-none'));
                        filtroPunto.classList.remove('d-none');
                    } else if(title === 'Encuesta') {
                        filtroPunto.classList.remove('d-none');
                    } else {
                        filtrosFecha.forEach(f => f.classList.remove('d-none'));
                        filtroPunto.classList.add('d-none');
                    }

                    modal.show();
                });
            });
        });
    </script>
</x-app-layout>
