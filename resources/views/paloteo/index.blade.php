<x-app-layout>
    <div class="inventory-container">
        
        <div class="controls-section">
            <a href="{{route('ordenCompra')}}" class="btn btn-warning mb-4">
                <i class="fa-solid fa-money-check-dollar"></i>Orden de Compra
            </a>
            <div class="semana-info">
                <div class="fecha-selector d-flex justify-content-center">
                    <select id="semanaHistorica" class="form-select">
                        <option value="">Semana actual</option>
                    </select>
                </div>
                <div class="fecha-rango">
                    <span>Semana del: <strong id="fechaInicio"></strong> al <strong id="fechaFin"></strong></span>
                    <button id="btnGuardarHistorico" class="btn-historico">
                        <i class="fas fa-save"></i> Guardar Semana Actual
                    </button>
                </div>
            </div>
            <div class="select-container">
                <select id="punto" class="form-select">
                    <option value="">Seleccione un punto</option>
                </select>
                
                <button id="downloadExcel" class="btn-download">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                </button>
            </div>
            <div id="encargadoSection" class="encargado-section" style="display: none;">
                <div class="encargado-header">
                    <h4>Encargado del punto:</h4>
                    <div class="encargado-content">
                        <input type="text" id="encargadoInput" class="form-control" placeholder="Nombre del encargado">
                        <!-- <button onclick="guardarEncargado()" class="btn-save">
                            <i class="fas fa-save"></i> Guardar
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="sections-container">
            <div class="inventory-section">
                <h3><i class="fas fa-utensils"></i> Almuerzos</h3>
                <div class="table-responsive">
                    <table class="inventory-table" id="table-almuerzos">
                        <thead>
                            <tr>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Lunes</th>
                                <th class="text-center">Martes</th>
                                <th class="text-center">Miércoles</th>
                                <th class="text-center">Jueves</th>
                                <th class="text-center">Viernes</th>
                                <th class="text-center">Sábado</th>
                                <th class="text-center">Domingo</th>
                            </tr>
                        </thead>
                        <tbody id="inventarioBody-4"  class="productosBody"></tbody>
                    </table>
                    <!-- Modal seleccionar producto -->
                    <div class="modal fade" id="modalProductos" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Seleccionar producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Producto</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="listaProductos">
                                <!-- Aquí se cargan los productos dinámicamente -->
                            </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="inventory-section">
                <h3><i class="fas fa-fire"></i> Parrilla</h3>
                <div class="table-responsive">
                    <table class="inventory-table" id="table-parrilla">
                        <thead>
                            <tr>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Lunes</th>
                                <th class="text-center">Martes</th>
                                <th class="text-center">Miércoles</th>
                                <th class="text-center">Jueves</th>
                                <th class="text-center">Viernes</th>
                                <th class="text-center">Sábado</th>
                                <th class="text-center">Domingo</th>
                            </tr>
                        </thead>
                        <tbody id="inventarioBody-1"  class="productosBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="inventory-section">
                <h3><i class="fas fa-utensils"></i> Cocina</h3>
                <div class="table-responsive">
                    <table class="inventory-table" id="table-cocina">
                        <thead>
                            <tr>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Lunes</th>
                                <th class="text-center">Martes</th>
                                <th class="text-center">Miércoles</th>
                                <th class="text-center">Jueves</th>
                                <th class="text-center">Viernes</th>
                                <th class="text-center">Sábado</th>
                                <th class="text-center">Domingo</th>
                            </tr>
                        </thead>
                        <tbody id="inventarioBody-2" class="productosBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="inventory-section">
                <h3><i class="fas fa-glass-martini-alt"></i> Bar</h3>
                <div class="table-responsive">
                    <table class="inventory-table" id="table-bar">
                        <thead>
                            <tr>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Lunes</th>
                                <th class="text-center">Martes</th>
                                <th class="text-center">Miércoles</th>
                                <th class="text-center">Jueves</th>
                                <th class="text-center">Viernes</th>
                                <th class="text-center">Sábado</th>
                                <th class="text-center">Domingo</th>
                            </tr>
                        </thead>
                        <tbody id="inventarioBody-3"  class="productosBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner"></div>
    </div>

    <div id="savedIndicator" class="saved-indicator">
        Guardado exitosamente
    </div>

    <script>
        const URL_PUNTOS = "{{ url('/paloteo/puntos') }}";
        const URL_GERENTE = "{{ url('/paloteo/gerente') }}";
        const BASE_URL = "{{ config('app.url') }}";
        const URL = "{{ url('/paloteo/productos') }}";
        const URL_GUARDAR = "{{ url('/guardar-inventario') }}";
        const URL_HISTORICO = "{{ url('/paloteo/historico') }}/";
        const URL_SAVE_HISTORICO = "{{ url('/guardar-historico') }}";
        const URL_CARGAR_HISTORICO = "{{ url('paloteo/cargar-historico') }}/";
    </script>
</x-app-layout>