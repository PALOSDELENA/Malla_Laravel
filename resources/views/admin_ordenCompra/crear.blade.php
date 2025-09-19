<x-app-layout>
    @php
        $proveedor_seleccionado = request('proveedor');
        $producto_nombre = request('producto'); 
    @endphp

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="container mt-2">
        {{-- Filtro de proveedores --}}
<form method="GET" class="form filter-container w-100">
    <div class="row w-100">
        <div class="col-md-4">
            <div class="filter-group">
                <label>Proveedor:</label>
                <select name="proveedor" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos los Proveedores</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->proFabricante }}" 
                            {{ $proveedor_seleccionado === $proveedor->proFabricante ? 'selected' : '' }}>
                            {{ $proveedor->proFabricante }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="filter-group">
                <label>Producto:</label>
                <input type="text" class="form-control" name="producto" value="{{ $producto_nombre }}" placeholder="Buscar producto..." onblur="this.form.submit()">
            </div>
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <a href="{{ route('crearOrden') }}" class="btn btn-secondary w-100">
                Limpiar Filtros
            </a>
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-warning w-100" onclick="limpiarDatosGuardados()">
                Limpiar Datos Guardados
            </button>
        </div>
    </div>
</form>
        {{-- Formulario de Orden de Compra --}}
<form method="POST" action="{{ route('registrarOrden') }}" class="form" id="ordenForm">
                @csrf
            <div class="row">
                <div class="col-md-6">
                    <label>Responsable:</label>
                    <input type="text" name="responsable" required><br>
                </div>
                <div class="col-md-6">
                    <label>Punto:</label>
                    <input type="hidden" name="punto_id" value="{{ $id_punto }}">
                    <input type="text" name="punto" value="{{$punto}}" readonly><br>
                </div>
                <div class="col-md-6">
                    <label>Fecha Entrega 1:</label>
                    <input type="date" name="fecha_entrega_1" required><br>
                </div>
                <div class="col-md-6">
                    <label>Fecha Entrega 2:</label>
                    <input type="date" name="fecha_entrega_2" required><br>
                </div>
                <div class="col-md-12">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="correo" required><br>
                </div>
            </div>

            <h2>Productos Solicitados</h2>
            
            @if ($productos->isEmpty())
                <p style="color:red;">⚠ No se encontraron productos para este punto y proveedor.</p>
            @else

                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Unidad de Medida</th>
                            <th>Inventario</th>
                            <th>Cantidad Bodega</th>
                            <th>Stock Mínimo</th>
                            <th>Sugerido</th>
                            <th>Pedido 1</th>
                            <th>Pedido 2</th>
                            <th>Total Pedido</th>
                            <th>Costo Unitario</th>
                            <th>Precio Total</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr data-id="{{ $producto->id }}">
                                <td>
                                    <input type="hidden" class="producto-id" value="{{ $producto->id }}">
                                    {{ $producto->proNombre }}
                                    <input type="hidden" name="productos[{{ $producto->id }}][id]" value="{{ $producto->id }}">
                                    <input type="hidden" name="productos[{{ $producto->id }}][proNombre]" 
                                           value="{{ $producto->proNombre }}">
                                </td>
                                <td>
                                    {{ $producto->proUnidadMedida }}
                                    <input type="hidden" name="productos[{{ $producto->id }}][proUnidadMedida]" 
                                           value="{{ $producto->proUnidadMedida }}">
                                </td>
                                <td><input type="number" name="productos[{{ $producto->id }}][inventario]" 
                                        data-product-id="{{ $producto->id }}"
                                        data-field="inventario">
                                </td>
                                <td>
                                    {{ $producto->cantidad_bodega }}
                                    <input type="hidden" name="productos[{{ $producto->id }}][cantidad_bodega]" 
                                        value="{{ $producto->cantidad_bodega }}"
                                        data-product-id="{{ $producto->id }}"
                                        data-field="cantidad_bodega">
                                </td>
                                <td>
                                    {{ $producto->stock_minimo }}
                                    <input type="hidden" name="productos[{{ $producto->id }}][stock_minimo]" 
                                           value="{{ $producto->stock_minimo }}">
                                </td>
                                <td><input type="number" name="productos[{{ $producto->id }}][sugerido]" readonly></td>
                                <td><input type="number" name="productos[{{ $producto->id }}][pedido_1]"
                                        data-product-id="{{ $producto->id }}"
                                        data-field="pedido_1">
                                </td>
                                <td><input type="number" name="productos[{{ $producto->id }}][pedido_2]"
                                        data-product-id="{{ $producto->id }}"
                                        data-field="pedido_2">
                                </td>
                                <td><input type="number" name="productos[{{ $producto->id }}][total_pedido]" readonly></td>
                                <td>
                                    {{ $producto->proPrecio }}
                                    <input type="hidden" name="productos[{{ $producto->id }}][proPrecio]" 
                                           value="{{ $producto->proPrecio }}">
                                </td>
                                <td><input type="number" name="productos[{{ $producto->id }}][precio_total]" readonly></td>
                                <td><input type="text" name="productos[{{ $producto->id }}][observaciones]"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Paginación de Laravel --}}
                {{ $productos->appends(['proveedor' => request('proveedor')])->links() }}
            @endif

            <button type="submit" class="button">Guardar Orden</button>
        </form>
    </div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const input = document.getElementById("filtroNombre");
    const tabla = document.querySelector(".tabla tbody");

    input.addEventListener("keyup", function() {
        const filtro = input.value.toLowerCase();
        const filas = tabla.getElementsByTagName("tr");

        for (let i = 0; i < filas.length; i++) {
            const celda = filas[i].getElementsByTagName("td")[0]; // primera columna (nombre del producto)
            if (celda) {
                const texto = celda.textContent || celda.innerText;
                filas[i].style.display = texto.toLowerCase().includes(filtro) ? "" : "none";
            }
        }
    });
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const localStorageKey = 'ordenCompraData';
        const form = document.getElementById('ordenForm');
        const inputs = document.querySelectorAll('.tabla input[data-product-id]');
        const limpiarBtn = document.querySelector('button[onclick="limpiarDatosGuardados()"]');
    
        // 1. Cargar datos al iniciar la página
        function loadData() {
            const savedData = JSON.parse(localStorage.getItem(localStorageKey)) || {};
            inputs.forEach(input => {
                const productId = input.dataset.productId;
                const field = input.dataset.field;
                if (savedData[productId] && savedData[productId][field] !== undefined) {
                    input.value = savedData[productId][field];
                }
            });
        }
    
        // 2. Guardar datos al cambiar un input
        function saveData(input) {
            let savedData = JSON.parse(localStorage.getItem(localStorageKey)) || {};
            const productId = input.dataset.productId;
            const field = input.dataset.field;
            
            if (!savedData[productId]) {
                savedData[productId] = {};
                savedData[productId]['id'] = productId;
            }

            // Usa el valor del input, incluso si está vacío
            savedData[productId][field] = input.value;
            localStorage.setItem(localStorageKey, JSON.stringify(savedData));
        }
    
        // 3. Manejar el envío del formulario
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Detiene el envío por defecto
    
                // Obtener todos los datos guardados en localStorage
                const allProductsData = JSON.parse(localStorage.getItem(localStorageKey)) || {};
                console.log("Datos enviados:", allProductsData);
                
                // Recorrer cada producto y añadir sus datos como inputs ocultos
                for (const productId in allProductsData) {
                    if (allProductsData.hasOwnProperty(productId)) {
                        const productData = allProductsData[productId];
                        for (const field in productData) {
                            if (productData.hasOwnProperty(field)) {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = `productos[${productId}][${field}]`;
                                hiddenInput.value = productData[field];
                                form.appendChild(hiddenInput);
                            }
                        }
                    }
                }
    
                // Enviar el formulario
                form.submit();
            });
        }
        
        // 4. Manejar el botón de limpiar
        if (limpiarBtn) {
            limpiarBtn.addEventListener('click', () => {
                localStorage.removeItem(localStorageKey);
                window.location.reload(); // Recarga la página para mostrar los campos vacíos
            });
        }
    
        // Event listener para guardar los datos al cambiar un input
        inputs.forEach(input => {
            input.addEventListener('input', (e) => saveData(e.target));
        });
    
        // Llamar a la función para cargar los datos al inicio
        loadData();
    });
</script>
</x-app-layout>
