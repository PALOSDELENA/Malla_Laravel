<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">

                <form id="formCotizacion" method="POST" action="{{ route('coti.store') }}">
                    @csrf
                    <div class="mb-3 d-flex align-items-start gap-2">
                        <div style="flex:1">
                            <label for="cliente_id" class="form-label fw-semibold">Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="form-select" required>
                                <option value="">Seleccione un cliente...</option>
                                @foreach(\App\Models\Cliente::all() as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->celular }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pt-4">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearCliente">
                                <i class="fa-solid fa-user-plus"></i> Nuevo
                            </button>
                        </div>
                    </div>

                    <!-- Datos del evento -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="motivo" class="form-label fw-semibold">Motivo</label>
                            <input type="text" name="motivo" id="motivo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sede" class="form-label fw-semibold">Sede</label>
                            <select name="sede" id="sede" class="form-select">
                                <option value="">Seleccione un cliente...</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fecha" class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="hora" class="form-label fw-semibold">Hora</label>
                            <input type="time" name="hora" id="hora" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="numero_personas" class="form-label fw-semibold">Número de personas</label>
                            <input type="number" name="numero_personas" id="numero_personas" class="form-control" min="1" value="1" required>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Items de la cotización</label>

                        <table class="table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width:60%">Producto</th>
                                    <th style="width:120px">Cantidad</th>
                                    <th style="width:120px">Precio</th>
                                    <th style="width:160px">Total Item</th>
                                    <th style="width:60px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][producto_id]" class="form-select item-producto" required>
                                            <option value="">Seleccione producto...</option>
                                            @foreach($productos as $prod)
                                                <option value="{{ $prod->id }}" data-precio="{{ $prod->proPrecio ?? 0 }}">{{ $prod->proNombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][cantidad]" class="form-control item-cantidad" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[0][precio]" class="item-precio-hidden" value="0.00">
                                        <input type="text" class="form-control item-precio-display" value="$0,00" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[0][total_item]" class="item-total-hidden" value="0.00" required>
                                        <input type="text" class="form-control item-total-display" value="$0,00" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove">-</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between">
                            <button type="button" id="btnAddItem" class="btn btn-sm btn-primary">Agregar item</button>
                            <small class="text-muted">Los precios se toman del catálogo de productos (campo `precio`).</small>
                        </div>
                    </div>

                    <!-- Totales y descuentos -->
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <label for="subtotal" class="form-label">Subtotal</label>
                            <input type="hidden" name="subtotal" id="subtotal_hidden" value="0.00">
                            <input type="text" id="subtotal_display" class="form-control" readonly value="$0,00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="descuento_pct" class="form-label">Descuento (%)</label>
                            <input type="number" step="0.01" name="descuento_pct" id="descuento_pct" class="form-control" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="descuento_monto" class="form-label">Descuento (monto)</label>
                            <input type="hidden" name="descuento_monto" id="descuento_monto_hidden" value="0.00">
                            <input type="text" id="descuento_monto_display" class="form-control" readonly value="$0,00">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ipoconsumo_toggle">
                                <label class="form-check-label fw-semibold" for="ipoconsumo_toggle">Ipo consumo</label>
                            </div>
                            <input type="hidden" name="ipoconsumo" id="ipoconsumo_hidden" value="0.00">
                            <input type="text" id="ipoconsumo_display" class="form-control mt-2" readonly value="$0,00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="reteica_toggle">
                                <label class="form-check-label fw-semibold" for="reteica_toggle">Reteica</label>
                            </div>
                            <input type="hidden" name="reteica" id="reteica_hidden" value="0.00">
                            <input type="text" id="reteica_display" class="form-control mt-2" readonly value="$0,00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="retefuente_toggle">
                                <label class="form-check-label fw-semibold" for="retefuente_toggle">Retefuente</label>
                            </div>
                            <input type="hidden" name="retefuente" id="retefuente_hidden" value="0.00">
                            <input type="text" id="retefuente_display" class="form-control mt-2" readonly value="$0,00">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="propina" class="form-label">Propina</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" step="0.01" name="propina" id="propina" class="form-control" value="0.00">
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input" type="checkbox" id="propina_pct_toggle">
                                    <label class="form-check-label small" for="propina_pct_toggle">usar % del subtotal</label>
                                </div>
                            </div>
                            <input type="hidden" name="propina_aplicado" id="propina_aplicado" value="0.00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="anticipo" class="form-label">Anticipo</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" step="0.01" name="anticipo" id="anticipo" class="form-control" value="0.00">
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input" type="checkbox" id="anticipo_pct_toggle">
                                    <label class="form-check-label small" for="anticipo_pct_toggle">usar % del subtotal</label>
                                </div>
                            </div>
                            <input type="hidden" name="anticipo_aplicado" id="anticipo_aplicado" value="0.00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="total_final" class="form-label">Total final</label>
                            <input type="hidden" name="total_final" id="total_final_hidden" value="0.00">
                            <input type="text" id="total_final_display" class="form-control" readonly value="$0,00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success">Guardar Cotización</button>
                    </div>
                </form>
            
                <!-- Modal Crear Cliente -->
                <div class="modal fade" id="modalCrearCliente" tabindex="-1" aria-labelledby="modalCrearClienteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form id="formCrearCliente">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalCrearClienteLabel">Registrar Cliente</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="clienteAlert" class="alert d-none" role="alert"></div>

                                    <div class="mb-3">
                                        <label for="nuevo_nombre" class="form-label">Nombre</label>
                                        <input type="text" id="nuevo_nombre" name="nombre" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nuevo_celular" class="form-label">Celular</label>
                                        <input type="text" id="nuevo_celular" name="celular" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nuevo_correo" class="form-label">Correo</label>
                                        <input type="email" id="nuevo_correo" name="correo" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary" id="btnGuardarCliente">Guardar Cliente</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const itemsTable = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
        const btnAdd = document.getElementById('btnAddItem');
        let index = 1; // ya existe un row con index 0

        function parseFloatSafe(v){
            const n = parseFloat(String(v).replace(/[^0-9\.-]+/g, ''));
            return isNaN(n)?0:n;
        }

        // Formatea número a formato local: miles con punto y decimales con coma, prefijo $
        function formatMoney(n){
            const parts = Number(n || 0).toFixed(2).split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return '$' + parts.join(',');
        }

        function recalcularFila(row){
            const select = row.querySelector('.item-producto');
            const cantidad = row.querySelector('.item-cantidad');
            const totalHidden = row.querySelector('.item-total-hidden');
            const totalDisplay = row.querySelector('.item-total-display');
            const precioHidden = row.querySelector('.item-precio-hidden');
            const precioDisplay = row.querySelector('.item-precio-display');
            const precio = parseFloatSafe(select.selectedOptions[0]?.dataset?.precio ?? 0);
            if (precioHidden) precioHidden.value = precio.toFixed(2);
            if (precioDisplay) precioDisplay.value = formatMoney(precio);
            const cant = parseFloatSafe(cantidad.value);
            const total = precio * cant;
            if (totalHidden) totalHidden.value = total.toFixed(2);
            if (totalDisplay) totalDisplay.value = formatMoney(total);
            recalcularTotales();
        }

        function recalcularTotales(){
            const rows = itemsTable.querySelectorAll('.item-row');
            let subtotal = 0;
            rows.forEach(r => {
                const v = r.querySelector('.item-total-hidden')?.value ?? '0';
                subtotal += parseFloatSafe(v);
            });

            // actualizar subtotal display + hidden
            const subtotalHidden = document.querySelector('input[name="subtotal"]');
            const subtotalDisplay = document.getElementById('subtotal_display');
            if (subtotalHidden) subtotalHidden.value = subtotal.toFixed(2);
            if (subtotalDisplay) subtotalDisplay.value = formatMoney(subtotal);

            const descuentoPct = parseFloatSafe(document.getElementById('descuento_pct').value);
            const descuentoMonto = subtotal * (descuentoPct/100);
            const descuentoHidden = document.querySelector('input[name="descuento_monto"]');
            const descuentoDisplay = document.getElementById('descuento_monto_display');
            if (descuentoHidden) descuentoHidden.value = descuentoMonto.toFixed(2);
            if (descuentoDisplay) descuentoDisplay.value = formatMoney(descuentoMonto);

            

            const propinaVal = parseFloatSafe(document.getElementById('propina')?.value);
            const anticipoVal = parseFloatSafe(document.getElementById('anticipo')?.value);
            const propinaToggle = document.getElementById('propina_pct_toggle');
            const anticipoToggle = document.getElementById('anticipo_pct_toggle');

            const propinaApplied = (propinaToggle && propinaToggle.checked) ? subtotal * (propinaVal/100) : propinaVal;
            const anticipoApplied = (anticipoToggle && anticipoToggle.checked) ? subtotal * (anticipoVal/100) : anticipoVal;

            // store applied amounts in hidden inputs for server
            const propinaHiddenApplied = document.getElementById('propina_aplicado');
            const anticipoHiddenApplied = document.getElementById('anticipo_aplicado');
            if (propinaHiddenApplied) propinaHiddenApplied.value = propinaApplied.toFixed(2);
            if (anticipoHiddenApplied) anticipoHiddenApplied.value = anticipoApplied.toFixed(2);

            // aplicar impuestos sólo si el toggle correspondiente está marcado
            const ipoconsumoToggle = document.getElementById('ipoconsumo_toggle');
            const reteicaToggle = document.getElementById('reteica_toggle');
            const retefuenteToggle = document.getElementById('retefuente_toggle');

            const ipoconsumo = (ipoconsumoToggle && ipoconsumoToggle.checked) ? subtotal * 0.05 : 0;
            const reteicaApplied = (reteicaToggle && reteicaToggle.checked) ? subtotal * 0.01 : 0;
            const retefuenteApplied = (retefuenteToggle && retefuenteToggle.checked) ? subtotal * 0.01 : 0;

            // actualizar hidden/display según toggles
            const ipoHidden = document.querySelector('input[name="ipoconsumo"]');
            const ipoDisplay = document.getElementById('ipoconsumo_display');
            if (ipoHidden) ipoHidden.value = ipoconsumo.toFixed(2);
            if (ipoDisplay) ipoDisplay.value = formatMoney(ipoconsumo);

            const reteHidden = document.querySelector('input[name="reteica"]');
            const reteDisplay = document.getElementById('reteica_display');
            if (reteHidden) reteHidden.value = reteicaApplied.toFixed(2);
            if (reteDisplay) reteDisplay.value = formatMoney(reteicaApplied);

            const retefuenteHidden = document.querySelector('input[name="retefuente"]');
            const retefuenteDisplay = document.getElementById('retefuente_display');
            if (retefuenteHidden) retefuenteHidden.value = retefuenteApplied.toFixed(2);
            if (retefuenteDisplay) retefuenteDisplay.value = formatMoney(retefuenteApplied);

            const total_final = subtotal - descuentoMonto + ipoconsumo + propinaApplied - anticipoApplied - reteicaApplied - retefuenteApplied;
            const totalHidden = document.querySelector('input[name="total_final"]');
            const totalDisplay = document.getElementById('total_final_display');
            if (totalHidden) totalHidden.value = total_final.toFixed(2);
            if (totalDisplay) totalDisplay.value = formatMoney(total_final);
        }

        // listeners: input (cantidad) and change (select producto)
        itemsTable.addEventListener('input', (e) => {
            const row = e.target.closest('.item-row');
            if (row) recalcularFila(row);
        });
        itemsTable.addEventListener('change', (e) => {
            const row = e.target.closest('.item-row');
            if (row) recalcularFila(row);
        });

        // add item
        btnAdd.addEventListener('click', () => {
            const template = document.querySelector('.item-row').cloneNode(true);
            // limpiar inputs (ajustar hidden y display)
            template.querySelectorAll('input').forEach(i => {
                if (i.classList.contains('item-cantidad')) i.value = 1;
                else if (i.classList.contains('item-precio-hidden')) i.value = '0.00';
                else if (i.classList.contains('item-precio-display')) i.value = '$0,00';
                else if (i.classList.contains('item-total-hidden')) i.value = '0.00';
                else if (i.classList.contains('item-total-display')) i.value = '$0,00';
                else i.value = '';
            });
            template.querySelectorAll('select').forEach(s => s.selectedIndex = 0);

            // actualizar names
            template.querySelectorAll('select, input').forEach(el => {
                const name = el.getAttribute('name');
                if (!name) return;
                const newName = name.replace(/items\[0\]/, `items[${index}]`);
                el.setAttribute('name', newName);
            });

            template.classList.add('item-row');
            itemsTable.appendChild(template);
            // recalcular fila recién añadida para sincronizar precios/total
            recalcularFila(template);
            index++;
        });

        // remove item
        itemsTable.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-remove')){
                const rows = itemsTable.querySelectorAll('.item-row');
                if (rows.length <= 1) return; // no eliminar último
                const row = e.target.closest('.item-row');
                row.remove();
                recalcularTotales();
            }
        });

        // recalcular al inicio
        recalcularTotales();

        // recalcular al cambiar descuentos/propina/anticipo y toggles de impuestos
        ['descuento_pct','propina','anticipo'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', recalcularTotales);
        });
        ['ipoconsumo_toggle','reteica_toggle','retefuente_toggle','propina_pct_toggle','anticipo_pct_toggle'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', recalcularTotales);
        });
    });
    </script>
    <script>
    // AJAX submit para crear cliente desde modal
    document.addEventListener('DOMContentLoaded', () => {
        const formCrear = document.getElementById('formCrearCliente');
        const alertBox = document.getElementById('clienteAlert');
        const modalEl = document.getElementById('modalCrearCliente');
        const modalBootstrap = bootstrap.Modal.getOrCreateInstance(modalEl);
        const clienteSelect = document.getElementById('cliente_id');

        if (!formCrear) return;

        formCrear.addEventListener('submit', async (e) => {
            e.preventDefault();
            alertBox.classList.add('d-none');
            const btn = document.getElementById('btnGuardarCliente');
            btn.disabled = true;

            const data = {
                nombre: document.getElementById('nuevo_nombre').value,
                celular: document.getElementById('nuevo_celular').value,
                correo: document.getElementById('nuevo_correo').value,
            };

            try {
                const res = await fetch("{{ route('clientes.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const json = await res.json();
                if (!res.ok) throw json;

                // Añadir al select y seleccionarlo
                const option = document.createElement('option');
                option.value = json.id;
                option.text = `${json.nombre} - ${json.celular ?? ''}`;
                clienteSelect.appendChild(option);
                clienteSelect.value = json.id;

                // Cerrar modal y resetear
                modalBootstrap.hide();
                formCrear.reset();
            } catch (err) {
                console.error(err);
                alertBox.classList.remove('d-none');
                alertBox.classList.remove('alert-success');
                alertBox.classList.remove('alert-danger');
                if (err && err.errors) {
                    // Validación
                    alertBox.classList.add('alert-danger');
                    alertBox.innerHTML = Object.values(err.errors).flat().join('<br>');
                } else if (err && err.message) {
                    alertBox.classList.add('alert-danger');
                    alertBox.textContent = err.message;
                } else {
                    alertBox.classList.add('alert-danger');
                    alertBox.textContent = 'Ocurrió un error al crear el cliente.';
                }
            } finally {
                btn.disabled = false;
            }
        });
    });
    </script>
</x-app-layout>
