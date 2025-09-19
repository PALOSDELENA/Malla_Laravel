<x-app-layout>
    <div class="container mt-2">
        <form method="POST" action="{{ route('ordenes.update', $orden->id) }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-6">
                    <div class="input-group">
                        <span class="input-group-text">Responsable</span>
                        <input type="text" class="form-control rounded" 
                            name="responsable" 
                            value="{{ old('responsable', $orden->responsable) }}" 
                            required>
                    </div>
                </div>

                <div class="col-md-6 mb-6">
                    <div class="input-group">
                        <span class="input-group-text">Correo</span>
                        <input type="email" class="form-control rounded" 
                            name="correo" 
                            value="{{ old('correo', $orden->email) }}" 
                            required>
                    </div>
                </div>

                <div class="col-md-6 mb-6">
                    <div class="input-group">
                        <span class="input-group-text">Fecha Entrega 1</span>
                        <input type="date" class="form-control rounded" 
                            name="fecha_entrega_1" 
                            value="{{ old('fecha_entrega_1', $orden->fecha_entrega_1) }}" 
                            required>
                    </div>
                </div>

                <div class="col-md-6 mb-6">
                    <div class="input-group">
                        <span class="input-group-text">Fecha Entrega 2</span>
                        <input type="date" class="form-control rounded" 
                            name="fecha_entrega_2" 
                            value="{{ old('fecha_entrega_2', $orden->fecha_entrega_2) }}" 
                            required>
                    </div>
                </div>
            </div>

            <table class="tabla">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Unidad de Medida</th>
                        <th>Inventario</th>
                        <th>Cantidad Bodega</th>
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
                    @foreach ($orden->producto as $producto)
                        <tr>
                            <td>{{ $producto->proNombre }}</td>
                            <td>{{ $producto->proUnidadMedida }}</td>
                            <td>
                                <input type="number" 
                                    name="productos[{{ $producto->id }}][inventario]" 
                                    value="{{ old("productos.{$producto->id}.inventario", $producto->pivot->inventario) }}"
                                    class="cantidad-input">
                                <input type="hidden" 
                                    name="productos[{{ $producto->id }}][cantidad_bodega]" 
                                    value="{{ $producto->pivot->cantidad_bodega }}">
                            </td>
                            <td>{{ $producto->pivot->cantidad_bodega }}</td>
                            <td class="sugerido">{{ $producto->pivot->sugerido }}</td>
                            <td>
                                <input type="number" 
                                    name="productos[{{ $producto->id }}][pedido_1]" 
                                    value="{{ old("productos.{$producto->id}.pedido_1", $producto->pivot->pedido_1) }}"
                                    class="cantidad-input">
                            </td>
                            <td>
                                <input type="number" 
                                    name="productos[{{ $producto->id }}][pedido_2]" 
                                    value="{{ old("productos.{$producto->id}.pedido_2", $producto->pivot->pedido_2) }}"
                                    class="cantidad-input">
                            </td>
                            <td class="total-pedido">
                                <span class="total-pedido-text"></span>
                                <input type="hidden" 
                                    name="productos[{{ $producto->id }}][total_pedido]" 
                                    value="{{ old("productos.{$producto->id}.total_pedido", $producto->pivot->pedido_1 + $producto->pivot->pedido_2) }}">
                            </td>
                            <td>
                                {{ $producto->proPrecio }}
                                <input type="hidden" 
                                    name="productos[{{ $producto->id }}][costo_unitario]" 
                                    value="{{ $producto->proPrecio }}">
                            </td>
                            <td class="precio-total">
                                <span class="precio-total-text">{{ $producto->pivot->precio_total }}</span>
                                <input type="hidden" 
                                    name="productos[{{ $producto->id }}][precio_total]" 
                                    value="{{ old("productos.{$producto->id}.precio_total", $producto->pivot->precio_total) }}">
                            </td>
                            <td>
                                <input type="text" 
                                    name="productos[{{ $producto->id }}][observaciones]" 
                                    value="{{ old("productos.{$producto->id}.observaciones", $producto->pivot->observaciones) }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="button-group">
                <button type="submit" class="button">Guardar Cambios</button>
                <a href="{{ route('ordenCompra') }}" class="button button-warning">Cancelar</a>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const pedido1 = row.querySelector('input[name*="[pedido_1]"]');
                const pedido2 = row.querySelector('input[name*="[pedido_2]"]');

                const totalPedidoText = row.querySelector('.total-pedido-text');
                const totalPedidoInput = row.querySelector('input[name*="[total_pedido]"]');

                const precioUnitario = parseFloat(row.querySelector('input[name*="[costo_unitario]"]').value) || 0;
                const precioTotalText = row.querySelector('.precio-total-text');
                const precioTotalInput = row.querySelector('input[name*="[precio_total]"]');

                function actualizarTotales() {
                    const p1 = parseFloat(pedido1.value) || 0;
                    const p2 = parseFloat(pedido2.value) || 0;

                    const totalPedido = p1 + p2;
                    const precioTotal = totalPedido * precioUnitario;

                    // Mostrar en tabla
                    totalPedidoText.textContent = totalPedido;
                    precioTotalText.textContent = precioTotal.toFixed(2);

                    // Guardar en inputs ocultos
                    totalPedidoInput.value = totalPedido;
                    precioTotalInput.value = precioTotal.toFixed(2);
                }

                // Eventos
                pedido1.addEventListener('input', actualizarTotales);
                pedido2.addEventListener('input', actualizarTotales);

                // Inicial
                actualizarTotales();
            });
        });
    </script>
</x-app-layout>
