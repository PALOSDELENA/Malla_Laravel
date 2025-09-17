<x-app-layout>
    @php
        $proveedor_seleccionado = request('proveedor'); 
    @endphp

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    <div class="container mt-2">

        {{-- Filtro de proveedores --}}
        <form method="GET" class="form filter-container">
            <div class="filter-group">
                <label>Proveedor:</label>
                <select name="proveedor" onchange="this.form.submit()">
                    <option value="">Todos los Proveedores</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->proFabricante }}" 
                            {{ $proveedor_seleccionado === $proveedor->proFabricante ? 'selected' : '' }}>
                            {{ $proveedor->proFabricante }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- Formulario de Orden de Compra --}}
        <form method="POST" action="{{ route('registrarOrden') }}" class="form">
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

            <button type="button" class="button" onclick="limpiarDatosGuardados()">Limpiar Datos Guardados</button>
            <h2>Productos Solicitados</h2>
            
            @if ($productos->isEmpty())
                <p style="color:red;">⚠ No se encontraron productos para este punto y proveedor.</p>
            @else
                <table class="table">
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
                            <tr>
                                <td>
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
                                <td><input type="number" name="productos[{{ $producto->id }}][inventario]"></td>
                                <td>
                                    {{ $producto->cantidad_bodega }}
                                    <input type="hidden" name="productos[{{ $producto->id }}][cantidad_bodega]" 
                                           value="{{ $producto->cantidad_bodega }}">
                                </td>
                                <td>
                                    {{ $producto->stock_minimo }}
                                    <input type="hidden" name="productos[{{ $producto->id }}][stock_minimo]" 
                                           value="{{ $producto->stock_minimo }}">
                                </td>
                                <td><input type="number" name="productos[{{ $producto->id }}][sugerido]" readonly></td>
                                <td><input type="number" name="productos[{{ $producto->id }}][pedido_1]"></td>
                                <td><input type="number" name="productos[{{ $producto->id }}][pedido_2]"></td>
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
        function limpiarDatosGuardados() {
            if (confirm('¿Estás seguro de querer limpiar todos los datos guardados?')) {
                localStorage.removeItem('ordenCompraTemp');
                location.reload();
            }
        }
    </script>
</x-app-layout>
