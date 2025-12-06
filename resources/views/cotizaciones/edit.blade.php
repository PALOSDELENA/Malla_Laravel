<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">

                <form id="formCotizacion" method="POST" action="{{ route('coti.update', $cot->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3 d-flex align-items-start gap-2">
                        <div style="flex:1">
                            <label for="cliente_id" class="form-label fw-semibold">Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="form-select" required>
                                <option value="">Seleccione un cliente...</option>
                                @foreach(\App\Models\Cliente::all() as $cliente)
                                    <option value="{{ $cliente->id }}" {{ $cliente->id == $cot->cliente_id ? 'selected' : '' }}>{{ $cliente->nombre }} - {{ $cliente->celular }}</option>
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
                            <input type="text" name="motivo" id="motivo" class="form-control" required value="{{ old('motivo', $cot->motivo) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sede" class="form-label fw-semibold">Sede</label>
                            <select name="sede" id="sede" class="form-select">
                                <option value="">Seleccione una sede...</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}" {{ $sede->id == $cot->sede ? 'selected' : '' }}>{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fecha" class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ old('fecha', (isset($cot->fecha) && is_object($cot->fecha) && method_exists($cot->fecha,'format')) ? $cot->fecha->format('Y-m-d') : (isset($cot->fecha) && $cot->fecha ? \Carbon\Carbon::parse($cot->fecha)->format('Y-m-d') : '')) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="hora" class="form-label fw-semibold">Hora</label>
                            <input type="time" name="hora" id="hora" class="form-control" required value="{{ old('hora', $cot->hora) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="numero_personas" class="form-label fw-semibold">Número de personas</label>
                            <input type="number" name="numero_personas" id="numero_personas" class="form-control" min="1" value="{{ old('numero_personas', $cot->numero_personas ?? 1) }}" required>
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
                                @foreach($cot->items as $i => $item)
                                <tr class="item-row">
                                    <td>
                                        <select name="items[{{ $i }}][producto_id]" class="form-select item-producto" required>
                                            <option value="">Seleccione producto...</option>
                                            @foreach($productos as $prod)
                                                <option value="{{ $prod->id }}" data-precio="{{ $prod->proPrecio ?? 0 }}" {{ $prod->id == $item->producto_id ? 'selected' : '' }}>{{ $prod->proNombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][cantidad]" class="form-control item-cantidad" min="1" value="{{ $item->cantidad }}" required>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $i }}][precio]" class="item-precio-hidden" value="{{ $item->producto_precio }}">
                                        <input type="text" class="form-control item-precio-display" value="${{ number_format($item->producto_precio,0,',','.') }}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[{{ $i }}][total_item]" class="item-total-hidden" value="{{ $item->total_item }}" required>
                                        <input type="text" class="form-control item-total-display" value="${{ number_format($item->total_item,0,',','.') }}" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove">-</button>
                                    </td>
                                </tr>
                                @endforeach
                                @if($cot->items->isEmpty())
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
                                        <input type="hidden" name="items[0][precio]" class="item-precio-hidden" value="0">
                                        <input type="text" class="form-control item-precio-display" value="$0" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[0][total_item]" class="item-total-hidden" value="0" required>
                                        <input type="text" class="form-control item-total-display" value="$0" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove">-</button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between">
                            <button type="button" id="btnAddItem" class="btn btn-sm btn-primary">Agregar item</button>
                            <small class="text-muted">Los precios se toman del catálogo de productos (campo `precio`).</small>
                        </div>
                    </div>

                    <!-- Items Extras -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Items Extras (Opcional)</label>
                        <small class="text-muted d-block mb-2">Agregue servicios adicionales, decoración u otros conceptos</small>
                        
                        <table class="table table-sm" id="extrasTable">
                            <thead>
                                <tr>
                                    <th style="width:25%">Concepto</th>
                                    <th style="width:10%">Cant.</th>
                                    <th style="width:15%">Valor Unit.</th>
                                    <th style="width:15%">Total</th>
                                    <th style="width:12%" class="text-center">Sumar al total</th>
                                    <th style="width:13%" class="text-center">Aplicar el Descuento</th>
                                    <th style="width:10%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cot->itemExtras as $index => $extra)
                                <tr class="extra-row">
                                    <td>
                                        <select name="extras[{{ $index }}][item_extra_id]" class="form-select form-select-sm extra-select">
                                            <option value="">Seleccione o escriba...</option>
                                            <option value="custom">✏️ Personalizado</option>
                                            @foreach($extras as $e)
                                                <option value="{{ $e->id }}" data-precio="{{ $e->precio ?? 0 }}" {{ $e->id == $extra->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="extras[{{ $index }}][nombre_custom]" 
                                               class="form-control form-control-sm mt-1 d-none extra-nombre-custom" 
                                               placeholder="Nombre del concepto"
                                               value="{{ $extra->pivot->nombre }}">
                                    </td>
                                    <td>
                                        <input type="number" name="extras[{{ $index }}][cantidad]" 
                                               class="form-control form-control-sm extra-cantidad" 
                                               value="{{ $extra->pivot->cantidad ?? 1 }}" min="1" step="1">
                                    </td>
                                    <td>
                                        <input type="number" name="extras[{{ $index }}][valor]" 
                                               class="form-control form-control-sm extra-valor" 
                                               step="0.01" value="{{ $extra->pivot->valor }}" min="0">
                                    </td>
                                    <td>
                                        <input type="text" 
                                               class="form-control form-control-sm extra-total" 
                                               value="${{ number_format($extra->pivot->valor * $extra->pivot->cantidad, 0, ',', '.') }}" 
                                               readonly>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input extra-suma" 
                                                   type="checkbox" 
                                                   name="extras[{{ $index }}][suma_al_total]" 
                                                   value="1" 
                                                   {{ $extra->pivot->suma_al_total ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input extra-descuento" 
                                                   type="checkbox" 
                                                   name="extras[{{ $index }}][aplicar_el_descuento]" 
                                                   value="1" 
                                                   {{ ($extra->pivot->aplicar_el_descuento ?? false) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-extra">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnAddExtra">
                            <i class="fa-solid fa-plus"></i> Agregar Extra
                        </button>
                    </div>

                    <!-- Totales y descuentos -->
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <label for="subtotal" class="form-label">Subtotal</label>
                            <input type="hidden" name="subtotal" id="subtotal_hidden" value="{{ old('subtotal', $cot->subtotal) }}">
                            <input type="text" id="subtotal_display" class="form-control" readonly value="${{ number_format($cot->subtotal ?? 0,0,',','.') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="descuento_pct" class="form-label">Descuento (%)</label>
                            <input type="number" step="0.01" name="descuento_pct" id="descuento_pct" class="form-control" value="{{ old('descuento_pct', $cot->descuento_pct ?? 0) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="descuento_monto" class="form-label">Descuento (monto)</label>
                            <input type="hidden" name="descuento_monto" id="descuento_monto_hidden" value="{{ old('descuento_monto', $cot->descuento_monto ?? 0) }}">
                            <input type="text" id="descuento_monto_display" class="form-control" readonly value="${{ number_format($cot->descuento_monto ?? 0,0,',','.') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ipoconsumo_toggle">
                                <label class="form-check-label fw-semibold" for="ipoconsumo_toggle">Ipo consumo</label>
                            </div>
                            <input type="hidden" name="ipoconsumo" id="ipoconsumo_hidden" value="{{ old('ipoconsumo', $cot->ipoconsumo ?? 0) }}">
                            <input type="text" id="ipoconsumo_display" class="form-control mt-2" readonly value="${{ number_format($cot->ipoconsumo ?? 0,0,',','.') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="reteica_toggle">
                                <label class="form-check-label fw-semibold" for="reteica_toggle">Reteica</label>
                            </div>
                            <input type="hidden" name="reteica" id="reteica_hidden" value="{{ old('reteica', $cot->reteica ?? 0) }}">
                            <input type="text" id="reteica_display" class="form-control mt-2" readonly value="${{ number_format($cot->reteica ?? 0,0,',','.') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="retefuente_toggle">
                                <label class="form-check-label fw-semibold" for="retefuente_toggle">Retefuente</label>
                            </div>
                            <input type="hidden" name="retefuente" id="retefuente_hidden" value="{{ old('retefuente', $cot->retefuente ?? 0) }}">
                            <input type="text" id="retefuente_display" class="form-control mt-2" readonly value="${{ number_format($cot->retefuente ?? 0,0,',','.') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="propina" class="form-label">Propina</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" step="0.01" name="propina" id="propina" class="form-control" value="{{ old('propina', $cot->propina ?? 0) }}">
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input" type="checkbox" id="propina_pct_toggle">
                                    <label class="form-check-label small" for="propina_pct_toggle">usar % del subtotal</label>
                                </div>
                            </div>
                            <input type="hidden" name="propina_aplicado" id="propina_aplicado" value="{{ old('propina_aplicado', $cot->propina ?? 0) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="anticipo" class="form-label">Anticipo</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" step="0.01" name="anticipo" id="anticipo" class="form-control" value="{{ old('anticipo', $cot->anticipo ?? 0) }}">
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input" type="checkbox" id="anticipo_pct_toggle">
                                    <label class="form-check-label small" for="anticipo_pct_toggle">usar % del subtotal</label>
                                </div>
                            </div>
                            <input type="hidden" name="anticipo_aplicado" id="anticipo_aplicado" value="{{ old('anticipo_aplicado', $cot->anticipo ?? 0) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="total_pendiente" class="form-label">Total Pendiente</label>
                            <input type="hidden" name="total_pendiente" id="total_pendiente_hidden" value="{{ old('total_pendiente', $cot->saldo_pendiente ?? 0) }}">
                            <input type="text" id="total_pendiente_display" class="form-control" readonly value="${{ number_format($cot->saldo_pendiente ?? 0,2,',','.') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="total_final" class="form-label">Total final</label>
                            <input type="hidden" name="total_final" id="total_final_hidden" value="{{ old('total_final', $cot->total_final ?? 0) }}">
                            <input type="text" id="total_final_display" class="form-control" readonly value="${{ number_format($cot->total_final ?? 0,0,',','.') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success">Actualizar Cotización</button>
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

    {{-- jQuery (required for Select2) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    // Function to initialize Select2 on a specific element
    function initializeSelect2(element) {
        $(element).select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione producto...',
            allowClear: true,
            width: '100%'
        });
    }

    function initializeSelect2Extra(element) {
        $(element).select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione o escriba...',
            allowClear: true,
            width: '100%'
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Select2 on cliente select
        $('#cliente_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione un cliente...',
            allowClear: true,
            width: '100%'
        });

        // Initialize existing product selects
        $('.item-producto').each(function() {
            initializeSelect2(this);
        });

        // Initialize existing extra selects
        $('.extra-select').each(function() {
            initializeSelect2Extra(this);
        });

        // Listen to Select2 change events for price calculation (Products)
        $(document).on('select2:select', '.item-producto', function(e) {
            const row = this.closest('.item-row');
            // Trigger change event manually so vanilla JS listener picks it up
            const event = new Event('change', { bubbles: true });
            this.dispatchEvent(event);
        });

        // Listen to Select2 change events for extras
        $(document).on('select2:select', '.extra-select', function(e) {
            const row = this.closest('.extra-row');
            const event = new Event('change', { bubbles: true });
            this.dispatchEvent(event);
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const itemsTable = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
        const btnAdd = document.getElementById('btnAddItem');
        let index = Math.max(1, @json($cot->items->count())); // start index based on existing items

        function parseFloatSafe(v){
            const n = parseFloat(String(v).replace(/[^0-9\\.-]+/g, ''));
            return isNaN(n)?0:n;
        }

        function formatMoney(n){
            const v = Math.ceil(Number(n || 0));
            return '$' + String(v).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function formatMoneyDecimal(n){
            const num = Number(n || 0).toFixed(2);
            const parts = String(num).split('.');
            const intPart = parts[0];
            const decPart = parts[1] || '00';
            const intWithThousands = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return '$' + intWithThousands + ',' + decPart;
        }

        function recalcularFila(row){
            const select = row.querySelector('.item-producto');
            const cantidad = row.querySelector('.item-cantidad');
            const totalHidden = row.querySelector('.item-total-hidden');
            const totalDisplay = row.querySelector('.item-total-display');
            const precioHidden = row.querySelector('.item-precio-hidden');
            const precioDisplay = row.querySelector('.item-precio-display');
            const precio = parseFloatSafe(select.selectedOptions[0]?.dataset?.precio ?? 0);
            const precioRounded = Math.ceil(precio);
            if (precioHidden) precioHidden.value = precioRounded.toString();
            if (precioDisplay) precioDisplay.value = formatMoney(precioRounded);
            const cant = parseFloatSafe(cantidad.value);
            const total = precio * cant;
            const totalRounded = Math.ceil(total);
            if (totalHidden) totalHidden.value = totalRounded.toString();
            if (totalDisplay) totalDisplay.value = formatMoney(totalRounded);
            recalcularTotales();
        }

        let __initialLoad = true;

        function recalcularTotales(){
            console.log('=== Recalculando Totales ===');
            const rows = itemsTable.querySelectorAll('.item-row');
            let subtotalProductos = 0;
            rows.forEach(r => {
                const v = r.querySelector('.item-total-hidden')?.value ?? '0';
                subtotalProductos += parseFloatSafe(v);
            });
            console.log('Subtotal de productos:', subtotalProductos);

            const descuentoPct = parseFloatSafe(document.getElementById('descuento_pct').value);
            console.log('Descuento %:', descuentoPct);

            // Agrupar items extras según sus checkboxes
            let extrasGrupoA = 0; // Solo "Sumar al Total"
            let extrasGrupoB = 0; // "Sumar al Total" + "Aplicar el Descuento"
            
            const extrasRows = document.querySelectorAll('.extra-row');
            extrasRows.forEach(row => {
                const sumaCheckbox = row.querySelector('.extra-suma');
                const descuentoCheckbox = row.querySelector('.extra-descuento');
                const valorInput = row.querySelector('.extra-valor');
                const cantidadInput = row.querySelector('.extra-cantidad');
                
                const suma = sumaCheckbox?.checked;
                const aplicarDescuento = descuentoCheckbox?.checked;
                const valorRaw = valorInput?.value || '0';
                const cantidadRaw = cantidadInput?.value || '1';
                
                if (suma) {
                    const valor = parseFloatSafe(valorRaw);
                    const cantidad = parseInt(cantidadRaw) || 1;
                    const totalLinea = valor * cantidad;
                    
                    if (aplicarDescuento) {
                        // Grupo B: ambos checkboxes marcados
                        console.log('Item extra Grupo B - Valor:', valor, 'Cant:', cantidad, 'Total:', totalLinea);
                        extrasGrupoB += totalLinea;
                    } else {
                        // Grupo A: solo "Sumar al Total"
                        console.log('Item extra Grupo A - Valor:', valor, 'Cant:', cantidad, 'Total:', totalLinea);
                        extrasGrupoA += totalLinea;
                    }
                }
            });
            
            console.log('Total Extras Grupo A (solo suma):', extrasGrupoA);
            console.log('Total Extras Grupo B (suma + descuento):', extrasGrupoB);

            let baseSubtotal;
            let baseConDescuento = 0; // Declarar fuera del bloque para usarlo después
            
            if (descuentoPct > 0) {
                // HAY DESCUENTO: aplicar la nueva lógica
                console.log('--- Aplicando lógica con descuento ---');
                
                // 1. Sumar productos + extras Grupo B
                const baseParaDescuento = subtotalProductos + extrasGrupoB;
                console.log('Base para descuento (productos + extras Grupo B):', baseParaDescuento);
                
                // 2. Dividir entre 1.08
                const baseParaDescuentoSinIVA = baseParaDescuento / 1.08;
                console.log('Base sin IVA (÷1.08):', baseParaDescuentoSinIVA);
                
                // 3. Aplicar descuento
                baseConDescuento = baseParaDescuentoSinIVA * (1 - (descuentoPct/100));
                console.log('Después del descuento:', baseConDescuento);
                
                // 4. Sumar extras Grupo A dividido entre 1.08
                const extrasGrupoASinIVA = extrasGrupoA / 1.08;
                console.log('Extras Grupo A sin IVA (÷1.08):', extrasGrupoASinIVA);
                
                // 5. El SUBTOTAL es la base SIN descuento + extras Grupo A
                baseSubtotal = Math.ceil(baseParaDescuentoSinIVA + extrasGrupoASinIVA);
                console.log('Subtotal (SIN descuento aplicado):', baseSubtotal);
                
            } else {
                // NO HAY DESCUENTO: mantener comportamiento actual
                console.log('--- Sin descuento: comportamiento actual ---');
                const totalExtras = extrasGrupoA + extrasGrupoB;
                const subtotalTotal = subtotalProductos + totalExtras;
                baseSubtotal = Math.ceil(subtotalTotal / 1.08);
                console.log('Subtotal (productos + todos los extras ÷1.08):', baseSubtotal);
            }

            // Actualizar subtotal en UI
            const subtotalHidden = document.querySelector('input[name="subtotal"]');
            const subtotalDisplay = document.getElementById('subtotal_display');
            if (subtotalHidden) subtotalHidden.value = baseSubtotal.toString();
            if (subtotalDisplay) subtotalDisplay.value = formatMoney(baseSubtotal);

            // Actualizar descuento monto display
            const descuentoHidden = document.querySelector('input[name="descuento_monto"]');
            const descuentoDisplay = document.getElementById('descuento_monto_display');
            
            if (descuentoPct > 0) {
                // Cuando hay descuento, el descuento_monto es baseConDescuento + extras Grupo A
                const extrasGrupoASinIVA = extrasGrupoA / 1.08;
                const descuentoMontoRedondeado = Math.ceil(baseConDescuento + extrasGrupoASinIVA);
                if (descuentoHidden) descuentoHidden.value = descuentoMontoRedondeado.toString();
                if (descuentoDisplay) descuentoDisplay.value = formatMoney(descuentoMontoRedondeado);
            } else {
                // Sin descuento, guardar el subtotal
                if (descuentoHidden) descuentoHidden.value = baseSubtotal.toString();
                if (descuentoDisplay) descuentoDisplay.value = formatMoney(baseSubtotal);
            }
            
            // effectiveBase es el descuento_monto (subtotal con descuento aplicado) para cálculos posteriores
            const effectiveBase = descuentoPct > 0 ? parseFloatSafe(descuentoHidden?.value) : baseSubtotal;

            const propinaVal = parseFloatSafe(document.getElementById('propina')?.value);
            const anticipoVal = parseFloatSafe(document.getElementById('anticipo')?.value);
            const propinaToggle = document.getElementById('propina_pct_toggle');
            const anticipoToggle = document.getElementById('anticipo_pct_toggle');

            const propinaAppliedRaw = (propinaToggle && propinaToggle.checked) ? effectiveBase * (propinaVal/100) : propinaVal;
            let propinaApplied = Math.ceil(propinaAppliedRaw);
            const propinaHiddenApplied = document.getElementById('propina_aplicado');
            // On initial load prefer stored value if present
            if (__initialLoad && propinaHiddenApplied && parseFloatSafe(propinaHiddenApplied.value) > 0) {
                propinaApplied = Math.ceil(parseFloatSafe(propinaHiddenApplied.value));
            }
            if (propinaHiddenApplied) propinaHiddenApplied.value = propinaApplied.toString();

            const ipoconsumoToggle = document.getElementById('ipoconsumo_toggle');
            const reteicaToggle = document.getElementById('reteica_toggle');
            const retefuenteToggle = document.getElementById('retefuente_toggle');

            const storedIpo = parseFloatSafe(document.getElementById('ipoconsumo_hidden')?.value);
            const storedReteica = parseFloatSafe(document.getElementById('reteica_hidden')?.value);
            const storedRetefuente = parseFloatSafe(document.getElementById('retefuente_hidden')?.value);

            const ipoconsumoRaw = (ipoconsumoToggle && ipoconsumoToggle.checked) ? effectiveBase * 0.08 : 0;
            const reteicaAppliedRaw = (reteicaToggle && reteicaToggle.checked) ? effectiveBase * 0.0138 : 0;
            const retefuenteAppliedRaw = (retefuenteToggle && retefuenteToggle.checked) ? effectiveBase * 0.035 : 0;

            let ipoconsumo = Math.ceil(ipoconsumoRaw);
            let reteicaApplied = Math.ceil(reteicaAppliedRaw);
            let retefuenteApplied = Math.ceil(retefuenteAppliedRaw);
            // On initial load prefer stored values if they exist and toggles are not checked
            if (__initialLoad) {
                if ((!ipoconsumoToggle || !ipoconsumoToggle.checked) && storedIpo > 0) ipoconsumo = Math.ceil(storedIpo);
                if ((!reteicaToggle || !reteicaToggle.checked) && storedReteica > 0) reteicaApplied = Math.ceil(storedReteica);
                if ((!retefuenteToggle || !retefuenteToggle.checked) && storedRetefuente > 0) retefuenteApplied = Math.ceil(storedRetefuente);
            }

            const ipoHidden = document.querySelector('input[name="ipoconsumo"]');
            const ipoDisplay = document.getElementById('ipoconsumo_display');
            if (ipoHidden) ipoHidden.value = ipoconsumo.toString();
            if (ipoDisplay) ipoDisplay.value = formatMoney(ipoconsumo);

            const reteHidden = document.querySelector('input[name="reteica"]');
            const reteDisplay = document.getElementById('reteica_display');
            if (reteHidden) reteHidden.value = reteicaApplied.toString();
            if (reteDisplay) reteDisplay.value = formatMoney(reteicaApplied);

            const retefuenteHidden = document.querySelector('input[name="retefuente"]');
            const retefuenteDisplay = document.getElementById('retefuente_display');
            if (retefuenteHidden) retefuenteHidden.value = retefuenteApplied.toString();
            if (retefuenteDisplay) retefuenteDisplay.value = formatMoney(retefuenteApplied);

            const total_final = effectiveBase + ipoconsumo + propinaApplied - reteicaApplied - retefuenteApplied;
            const totalHidden = document.querySelector('input[name="total_final"]');
            const totalDisplay = document.getElementById('total_final_display');
            const totalFinalCeil = Math.ceil(total_final);
            if (totalHidden) totalHidden.value = totalFinalCeil.toString();
            if (totalDisplay) totalDisplay.value = formatMoney(totalFinalCeil);

            const storedAnticipo = parseFloatSafe(document.getElementById('anticipo_aplicado')?.value);
            const anticipoAppliedRaw = (anticipoToggle && anticipoToggle.checked) ? totalFinalCeil * (anticipoVal/100) : anticipoVal;
            let anticipoApplied = Number(anticipoAppliedRaw);
            // On initial load prefer stored applied anticipo if present
            if (__initialLoad && storedAnticipo > 0) {
                anticipoApplied = Number(storedAnticipo);
            }
            const anticipoHiddenApplied = document.getElementById('anticipo_aplicado');
            if (anticipoHiddenApplied) anticipoHiddenApplied.value = anticipoApplied.toFixed(2);

            // Set visible anticipo input: if percent-toggle is checked, show percentage, otherwise show fixed amount
            try {
                const anticipoInput = document.getElementById('anticipo');
                if (anticipoInput) {
                    if (anticipoToggle && anticipoToggle.checked) {
                        // Avoid division by zero
                        if (Number(totalFinalCeil) > 0) {
                            const pct = (Number(anticipoApplied) / Number(totalFinalCeil)) * 100;
                            // Show percentage without trailing zeros
                            const pctFixed = Number(pct).toFixed(2);
                            anticipoInput.value = isNaN(pct) ? '' : parseFloat(pctFixed);
                        } else {
                            anticipoInput.value = '';
                        }
                    } else {
                        // Show fixed amount with 2 decimals
                        anticipoInput.value = isNaN(anticipoApplied) ? '' : Number(anticipoApplied).toFixed(2);
                    }
                }
            } catch (err) {
                console.error('setting visible anticipo failed', err);
            }

            const total_pendiente = Number(totalFinalCeil) - anticipoApplied;
            const totalPendienteHidden = document.querySelector('input[name="total_pendiente"]');
            const totalPendienteDisplay = document.getElementById('total_pendiente_display');
            if (totalPendienteHidden) totalPendienteHidden.value = Number(total_pendiente).toFixed(2);
            if (totalPendienteDisplay) totalPendienteDisplay.value = formatMoneyDecimal(total_pendiente);
            // After first full calculation, unset initial flag so further changes recompute normally
            if (__initialLoad) __initialLoad = false;
        }

        function applyInitialToggles(){
            try {
                const ipoconsumoToggle = document.getElementById('ipoconsumo_toggle');
                const reteicaToggle = document.getElementById('reteica_toggle');
                const retefuenteToggle = document.getElementById('retefuente_toggle');
                const propinaPctToggle = document.getElementById('propina_pct_toggle');
                const anticipoPctToggle = document.getElementById('anticipo_pct_toggle');

                const storedIpo = parseFloatSafe(document.getElementById('ipoconsumo_hidden')?.value);
                const storedReteica = parseFloatSafe(document.getElementById('reteica_hidden')?.value);
                const storedRetefuente = parseFloatSafe(document.getElementById('retefuente_hidden')?.value);
                const storedPropina = parseFloatSafe(document.getElementById('propina_aplicado')?.value);
                const storedAnticipo = parseFloatSafe(document.getElementById('anticipo_aplicado')?.value);

                if (ipoconsumoToggle && storedIpo > 0) ipoconsumoToggle.checked = true;
                if (reteicaToggle && storedReteica > 0) reteicaToggle.checked = true;
                if (retefuenteToggle && storedRetefuente > 0) retefuenteToggle.checked = true;

                // No activar automáticamente los toggles de porcentaje para propina y anticipo
                // ya que al editar cargamos el valor monetario fijo.
                // if (propinaPctToggle && storedPropina > 0) propinaPctToggle.checked = true;
                // if (anticipoPctToggle && storedAnticipo > 0) anticipoPctToggle.checked = true;
            } catch (err) {
                console.error('applyInitialToggles error', err);
            }
        }

        itemsTable.addEventListener('input', (e) => {
            const row = e.target.closest('.item-row');
            if (row) recalcularFila(row);
        });
        itemsTable.addEventListener('change', (e) => {
            const row = e.target.closest('.item-row');
            if (row) recalcularFila(row);
        });

        btnAdd.addEventListener('click', () => {
            // Get the first row as template
            const firstRow = document.querySelector('.item-row');
            const firstSelect = firstRow.querySelector('.item-producto');
            
            // Destroy Select2 temporarily to clone clean HTML
            if ($(firstSelect).data('select2')) {
                $(firstSelect).select2('destroy');
            }
            
            // Clone the row
            const template = firstRow.cloneNode(true);
            
            // Re-initialize Select2 on the first row
            initializeSelect2(firstSelect);
            
            // Clean the cloned row
            template.querySelectorAll('input').forEach(i => {
                if (i.classList.contains('item-cantidad')) i.value = 1;
                else if (i.classList.contains('item-precio-hidden')) i.value = '0';
                else if (i.classList.contains('item-precio-display')) i.value = '$0';
                else if (i.classList.contains('item-total-hidden')) i.value = '0';
                else if (i.classList.contains('item-total-display')) i.value = '$0';
                else i.value = '';
            });
            
            // Reset select to first option
            const clonedSelect = template.querySelector('.item-producto');
            if (clonedSelect) {
                clonedSelect.selectedIndex = 0;
                clonedSelect.value = '';
            }

            // Update names
            template.querySelectorAll('select, input').forEach(el => {
                const name = el.getAttribute('name');
                if (!name) return;
                const newName = name.replace(/items\[0\]/, `items[${index}]`);
                el.setAttribute('name', newName);
            });

            template.classList.add('item-row');
            itemsTable.appendChild(template);
            
            // Initialize Select2 on the new select
            if (clonedSelect) {
                initializeSelect2(clonedSelect);
            }
            
            recalcularFila(template);
            index++;
        });

        itemsTable.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-remove')){
                const row = e.target.closest('.item-row');
                row.remove();
                recalcularTotales();
            }
        });

        // apply initial toggles based on saved hidden values, then recalculate
        applyInitialToggles();
        recalcularTotales();

        ['descuento_pct','propina','anticipo'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', recalcularTotales);
        });
        
        // Listeners para impuestos (excluyendo propina y anticipo que tienen lógica especial)
        ['ipoconsumo_toggle','reteica_toggle','retefuente_toggle'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', recalcularTotales);
        });

        // Lógica especial para alternar entre Valor y Porcentaje (Propina y Anticipo)
        function handlePercentToggle(e) {
            const toggle = e.target;
            const isChecked = toggle.checked;
            const type = toggle.id.replace('_pct_toggle', ''); // 'propina' or 'anticipo'
            const input = document.getElementById(type);
            const val = parseFloatSafe(input.value);
            
            let base = 0;
            
            if (type === 'propina') {
                // Base para propina: Subtotal - Descuento (effectiveBase)
                const subtotal = parseFloatSafe(document.querySelector('input[name="subtotal"]')?.value);
                const descuentoPct = parseFloatSafe(document.getElementById('descuento_pct')?.value);
                // Replicar lógica de effectiveBase
                const descuentoMontoRaw = subtotal * (1 - (descuentoPct/100));
                const descuentoMonto = Math.ceil(descuentoMontoRaw);
                base = (descuentoPct > 0) ? descuentoMonto : subtotal;
            } else if (type === 'anticipo') {
                // Base para anticipo: Total Final
                base = parseFloatSafe(document.querySelector('input[name="total_final"]')?.value);
            }
            
            if (base === 0) {
                recalcularTotales();
                return;
            }
            
            if (isChecked) {
                // Convertir $ a %: (Valor / Base) * 100
                const pct = (val / base) * 100;
                // parseFloat elimina los ceros no significativos (10.00 -> 10)
                input.value = parseFloat(pct.toFixed(2));
            } else {
                // Convertir % a $: (Valor / 100) * Base
                const amount = (val / 100) * base;
                input.value = Math.ceil(amount);
            }
            
            recalcularTotales();
        }

        document.getElementById('propina_pct_toggle').addEventListener('change', handlePercentToggle);
        document.getElementById('anticipo_pct_toggle').addEventListener('change', handlePercentToggle);

        // Inicializar Propina y Anticipo en modo porcentaje si tienen valor
        function applyInitialPercentModes() {
             const propinaPctToggle = document.getElementById('propina_pct_toggle');
             const anticipoPctToggle = document.getElementById('anticipo_pct_toggle');
             const storedPropina = parseFloatSafe(document.getElementById('propina_aplicado')?.value);
             const storedAnticipo = parseFloatSafe(document.getElementById('anticipo_aplicado')?.value);
             
             if (propinaPctToggle && storedPropina > 0) {
                 propinaPctToggle.checked = true;
                 // Trigger conversion manually
                 const event = { target: propinaPctToggle };
                 handlePercentToggle(event);
             }
             
             if (anticipoPctToggle && storedAnticipo > 0) {
                 anticipoPctToggle.checked = true;
                 // Trigger conversion manually
                 const event = { target: anticipoPctToggle };
                 handlePercentToggle(event);
             }
        }
        applyInitialPercentModes();

        // Lógica para Items Extras
        const extrasTable = document.getElementById('extrasTable').getElementsByTagName('tbody')[0];
        const btnAddExtra = document.getElementById('btnAddExtra');
        let extraIndex = {{ $cot->itemExtras->count() > 0 ? $cot->itemExtras->count() : 0 }};

        function createExtraRow(index) {
            const row = document.createElement('tr');
            row.classList.add('extra-row');
            row.innerHTML = `
                <td>
                    <select name="extras[${index}][item_extra_id]" class="form-select form-select-sm extra-select">
                        <option value="">Seleccione o escriba...</option>
                        <option value="custom">✏️ Personalizado</option>
                        @foreach($extras as $extra)
                            <option value="{{ $extra->id }}" data-precio="{{ $extra->precio ?? 0 }}">{{ $extra->nombre }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="extras[${index}][nombre_custom]" 
                           class="form-control form-control-sm mt-1 d-none extra-nombre-custom" 
                           placeholder="Nombre del concepto">
                </td>
                <td>
                    <input type="number" name="extras[${index}][cantidad]" 
                           class="form-control form-control-sm extra-cantidad" 
                           value="1" min="1" step="1">
                </td>
                <td>
                    <input type="number" name="extras[${index}][valor]" 
                           class="form-control form-control-sm extra-valor" 
                           step="0.01" value="0" min="0">
                </td>
                <td>
                    <input type="text" 
                           class="form-control form-control-sm extra-total" 
                           value="$0" 
                           readonly>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input extra-suma" 
                               type="checkbox" 
                               name="extras[${index}][suma_al_total]" 
                               value="1" 
                               >
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input extra-descuento" 
                               type="checkbox" 
                               name="extras[${index}][aplicar_el_descuento]" 
                               value="1" 
                               >
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-extra">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            return row;
        }

        if (btnAddExtra) {
            btnAddExtra.addEventListener('click', () => {
                const row = createExtraRow(extraIndex);
                extrasTable.appendChild(row);
                
                // Initialize Select2 on the new extra select
                const newSelect = row.querySelector('.extra-select');
                if (newSelect) {
                    initializeSelect2Extra(newSelect);
                }
                
                extraIndex++;
            });
        }

        // Delegación de eventos para extras
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-remove-extra')) {
                const row = e.target.closest('.extra-row');
                if (row) {
                    row.remove();
                    recalcularTotales();
                }
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('extra-select')) {
                const select = e.target;
                const row = select.closest('tr');
                const customInput = row.querySelector('.extra-nombre-custom');
                const valorInput = row.querySelector('.extra-valor');
                
                if (select.value === 'custom') {
                    customInput.classList.remove('d-none');
                    customInput.required = true;
                    valorInput.value = '';
                } else {
                    customInput.classList.add('d-none');
                    customInput.required = false;
                    // Obtener precio del data-attribute
                    const option = select.options[select.selectedIndex];
                    const precio = option.dataset.precio || 0;
                    valorInput.value = precio;
                }
                recalcularTotales();
            }
            
            if (e.target.classList.contains('extra-suma') || e.target.classList.contains('extra-descuento')) {
                recalcularTotales();
            }
        });

        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('extra-valor') || e.target.classList.contains('extra-cantidad')) {
                // Actualizar el campo Total de la fila
                const row = e.target.closest('.extra-row');
                if (row) {
                    const cantidadInput = row.querySelector('.extra-cantidad');
                    const valorInput = row.querySelector('.extra-valor');
                    const totalInput = row.querySelector('.extra-total');
                    
                    if (cantidadInput && valorInput && totalInput) {
                        const cantidad = parseFloat(cantidadInput.value) || 0;
                        const valor = parseFloat(valorInput.value) || 0;
                        const total = cantidad * valor;
                        totalInput.value = formatMoney(total);
                    }
                }
                
                recalcularTotales();
            }
        });
    });
    </script>

    <script>
    // AJAX submit para crear cliente desde modal (same as create)
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

                const option = document.createElement('option');
                option.value = json.id;
                option.text = `${json.nombre} - ${json.celular ?? ''}`;
                clienteSelect.appendChild(option);
                clienteSelect.value = json.id;

                modalBootstrap.hide();
                formCrear.reset();
            } catch (err) {
                console.error(err);
                alertBox.classList.remove('d-none');
                alertBox.classList.remove('alert-success');
                alertBox.classList.remove('alert-danger');
                if (err && err.errors) {
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
