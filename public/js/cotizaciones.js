/**
 * Lógica compartida para creación y edición de cotizaciones
 */

let itemsTableBody;
let extrasTableBody;
let itemIndex = 0;
let extraIndex = 0;

// Utilidades
function parseFloatSafe(v) {
    const n = parseFloat(String(v).replace(/[^0-9\.-]+/g, ''));
    return isNaN(n) ? 0 : n;
}

function formatMoney(n) {
    const v = Math.ceil(Number(n || 0));
    return '$' + String(v).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function formatMoneyDecimal(n) {
    const num = Number(n || 0).toFixed(2);
    const parts = String(num).split('.');
    const intPart = parts[0];
    const decPart = parts[1] || '00';
    const intWithThousands = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return '$' + intWithThousands + ',' + decPart;
}

// Lógica de Filas de Productos
function recalcularFila(row) {
    const select = row.querySelector('.item-producto');
    const cantidad = row.querySelector('.item-cantidad');
    const totalHidden = row.querySelector('.item-total-hidden');
    const totalDisplay = row.querySelector('.item-total-display');
    const precioHidden = row.querySelector('.item-precio-hidden');
    const precioDisplay = row.querySelector('.item-precio-display');

    // Obtener precio del data-attribute
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

// Lógica Principal de Totales
function recalcularTotales() {
    // 1. Sumar productos
    const rows = itemsTableBody.querySelectorAll('.item-row');
    let subtotal = 0;
    rows.forEach(r => {
        const v = r.querySelector('.item-total-hidden')?.value ?? '0';
        subtotal += parseFloatSafe(v);
    });

    // 2. Sumar items extras
    let totalExtras = 0;
    const extrasRows = document.querySelectorAll('.extra-row');
    extrasRows.forEach(row => {
        const sumaCheckbox = row.querySelector('.extra-suma');
        const valorInput = row.querySelector('.extra-valor');
        const cantidadInput = row.querySelector('.extra-cantidad');

        const suma = sumaCheckbox?.checked;
        const valorRaw = valorInput?.value || '0';
        const cantidadRaw = cantidadInput?.value || '1';

        if (suma) {
            const valor = parseFloatSafe(valorRaw);
            const cantidad = parseInt(cantidadRaw) || 1;
            const totalLinea = valor * cantidad;
            totalExtras += totalLinea;
        }
    });

    subtotal += totalExtras;

    // 3. Calcular base y mostrar subtotal
    const baseSubtotalRaw = subtotal / 1.08;
    const baseSubtotal = Math.ceil(baseSubtotalRaw);

    const subtotalHidden = document.querySelector('input[name="subtotal"]');
    const subtotalDisplay = document.getElementById('subtotal_display');
    if (subtotalHidden) subtotalHidden.value = baseSubtotal.toString();
    if (subtotalDisplay) subtotalDisplay.value = formatMoney(baseSubtotal);

    // 4. Descuentos
    const descuentoPct = parseFloatSafe(document.getElementById('descuento_pct').value);
    const descuentoMontoRaw = baseSubtotal * (1 - (descuentoPct / 100));
    const descuentoMonto = Math.ceil(descuentoMontoRaw);
    const effectiveBase = (descuentoPct > 0) ? descuentoMonto : baseSubtotal;

    const descuentoHidden = document.querySelector('input[name="descuento_monto"]');
    const descuentoDisplay = document.getElementById('descuento_monto_display');
    if (descuentoHidden) descuentoHidden.value = descuentoMonto.toString();
    if (descuentoPct > 0) descuentoDisplay.value = formatMoney(descuentoMonto);

    // 5. Impuestos y Adicionales
    const propinaVal = parseFloatSafe(document.getElementById('propina')?.value);
    const anticipoVal = parseFloatSafe(document.getElementById('anticipo')?.value);

    const propinaToggle = document.getElementById('propina_pct_toggle');
    const anticipoToggle = document.getElementById('anticipo_pct_toggle');
    const ipoconsumoToggle = document.getElementById('ipoconsumo_toggle');
    const reteicaToggle = document.getElementById('reteica_toggle');
    const retefuenteToggle = document.getElementById('retefuente_toggle');

    // Cálculos condicionales
    const propinaAppliedRaw = (propinaToggle && propinaToggle.checked) ? effectiveBase * (propinaVal / 100) : propinaVal;
    let propinaApplied = Math.ceil(propinaAppliedRaw);

    const ipoconsumoRaw = (ipoconsumoToggle && ipoconsumoToggle.checked) ? effectiveBase * 0.08 : 0;
    const reteicaAppliedRaw = (reteicaToggle && reteicaToggle.checked) ? effectiveBase * 0.0138 : 0;
    const retefuenteAppliedRaw = (retefuenteToggle && retefuenteToggle.checked) ? effectiveBase * 0.035 : 0;

    let ipoconsumo = Math.ceil(ipoconsumoRaw);
    let reteicaApplied = Math.ceil(reteicaAppliedRaw);
    let retefuenteApplied = Math.ceil(retefuenteAppliedRaw);

    // Actualizar inputs ocultos y displays de impuestos
    updateField('propina_aplicado', null, propinaApplied);
    updateField('ipoconsumo', 'ipoconsumo_display', ipoconsumo);
    updateField('reteica', 'reteica_display', reteicaApplied);
    updateField('retefuente', 'retefuente_display', retefuenteApplied);

    // 6. Total Final
    const total_final = effectiveBase + ipoconsumo + propinaApplied - reteicaApplied - retefuenteApplied;
    const totalFinalCeil = Math.ceil(total_final);

    updateField('total_final', 'total_final_display', totalFinalCeil);

    // 7. Anticipo y Saldo Pendiente
    const anticipoAppliedRaw = (anticipoToggle && anticipoToggle.checked) ? totalFinalCeil * (anticipoVal / 100) : anticipoVal;
    let anticipoApplied = Number(anticipoAppliedRaw);

    updateField('anticipo_aplicado', null, anticipoApplied.toFixed(2));

    // Actualizar input visible de anticipo si es porcentaje
    updateVisibleAnticipoInput(anticipoToggle, anticipoVal, anticipoApplied, totalFinalCeil);

    const total_pendiente = Number(totalFinalCeil) - anticipoApplied;

    const totalPendienteHidden = document.querySelector('input[name="total_pendiente"]');
    const totalPendienteDisplay = document.getElementById('total_pendiente_display');
    if (totalPendienteHidden) totalPendienteHidden.value = Number(total_pendiente).toFixed(2);
    if (totalPendienteDisplay) totalPendienteDisplay.value = formatMoneyDecimal(total_pendiente);
}

function updateField(hiddenName, displayId, value) {
    const hidden = document.querySelector(`input[name="${hiddenName}"]`) || document.getElementById(hiddenName);
    const display = displayId ? document.getElementById(displayId) : null;

    if (hidden) hidden.value = value.toString();
    if (display) display.value = formatMoney(value);
}

function updateVisibleAnticipoInput(toggle, val, applied, total) {
    try {
        const anticipoInput = document.getElementById('anticipo');
        if (anticipoInput && toggle && toggle.checked) {
            if (Number(total) > 0) {
                const pct = (Number(applied) / Number(total)) * 100;
                anticipoInput.value = isNaN(pct) ? '' : Number(pct).toFixed(2);
            } else {
                anticipoInput.value = '';
            }
        }
    } catch (err) {
        console.error(err);
    }
}

function applyInitialToggles() {
    try {
        const ipoconsumoToggle = document.getElementById('ipoconsumo_toggle');
        const reteicaToggle = document.getElementById('reteica_toggle');
        const retefuenteToggle = document.getElementById('retefuente_toggle');

        const storedIpo = parseFloatSafe(document.getElementById('ipoconsumo_hidden')?.value);
        const storedReteica = parseFloatSafe(document.getElementById('reteica_hidden')?.value);
        const storedRetefuente = parseFloatSafe(document.getElementById('retefuente_hidden')?.value);

        if (ipoconsumoToggle && storedIpo > 0) ipoconsumoToggle.checked = true;
        if (reteicaToggle && storedReteica > 0) reteicaToggle.checked = true;
        if (retefuenteToggle && storedRetefuente > 0) retefuenteToggle.checked = true;

        // Nota: Propina y Anticipo NO se activan automáticamente para respetar valores fijos al editar
    } catch (err) {
        console.error('applyInitialToggles error', err);
    }
}

// Funciones de creación de filas
function createExtraRowHTML(index, extrasList) {
    let options = '<option value="">Seleccione o escriba...</option><option value="custom">✏️ Personalizado</option>';
    extrasList.forEach(extra => {
        options += `<option value="${extra.id}" data-precio="${extra.precio ?? 0}">${extra.nombre}</option>`;
    });

    const row = document.createElement('tr');
    row.classList.add('extra-row');
    row.innerHTML = `
        <td>
            <select name="extras[${index}][item_extra_id]" class="form-select form-select-sm extra-select">
                ${options}
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
            <button type="button" class="btn btn-sm btn-danger btn-remove-extra">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    `;
    return row;
}

// Inicialización
function initCotizacion(config) {
    itemsTableBody = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
    extrasTableBody = document.getElementById('extrasTable').getElementsByTagName('tbody')[0];
    const btnAddItem = document.getElementById('btnAddItem');
    const btnAddExtra = document.getElementById('btnAddExtra');

    itemIndex = config.itemIndex || 1;
    extraIndex = config.extraIndex || 0;
    const extrasList = config.extrasList || [];

    // Listeners Productos
    itemsTableBody.addEventListener('input', (e) => {
        const row = e.target.closest('.item-row');
        if (row) recalcularFila(row);
    });
    itemsTableBody.addEventListener('change', (e) => {
        const row = e.target.closest('.item-row');
        if (row) recalcularFila(row);
    });
    itemsTableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-remove')) {
            const rows = itemsTableBody.querySelectorAll('.item-row');
            if (rows.length <= 1) return;
            e.target.closest('.item-row').remove();
            recalcularTotales();
        }
    });

    btnAddItem.addEventListener('click', () => {
        const template = document.querySelector('.item-row').cloneNode(true);
        template.querySelectorAll('input').forEach(i => {
            if (i.classList.contains('item-cantidad')) i.value = 1;
            else if (i.classList.contains('item-precio-hidden') || i.classList.contains('item-total-hidden')) i.value = '0';
            else if (i.classList.contains('item-precio-display') || i.classList.contains('item-total-display')) i.value = '$0';
            else i.value = '';
        });
        template.querySelectorAll('select').forEach(s => s.selectedIndex = 0);

        // Actualizar índices
        template.querySelectorAll('select, input').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                // Reemplaza cualquier índice [numero] por el nuevo índice
                const newName = name.replace(/items\[\d+\]/, `items[${itemIndex}]`);
                el.setAttribute('name', newName);
            }
        });

        itemsTableBody.appendChild(template);
        recalcularFila(template);
        itemIndex++;
    });

    // Listeners Extras
    if (btnAddExtra) {
        btnAddExtra.addEventListener('click', () => {
            const row = createExtraRowHTML(extraIndex, extrasList);
            extrasTableBody.appendChild(row);
            extraIndex++;
            recalcularTotales();
        });
    }

    document.addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-extra')) {
            e.target.closest('.extra-row').remove();
            recalcularTotales();
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
                const option = select.options[select.selectedIndex];
                const precio = option.dataset.precio || 0;
                valorInput.value = precio;
            }
            recalcularTotales();
        }
        if (e.target.classList.contains('extra-suma')) {
            recalcularTotales();
        }
    });

    document.addEventListener('input', (e) => {
        if (e.target.classList.contains('extra-valor') || e.target.classList.contains('extra-cantidad')) {
            recalcularTotales();
        }
    });

    // Listeners Generales
    ['descuento_pct', 'propina', 'anticipo'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', recalcularTotales);
    });
    ['ipoconsumo_toggle', 'reteica_toggle', 'retefuente_toggle', 'propina_pct_toggle', 'anticipo_pct_toggle'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', recalcularTotales);
    });

    // Inicialización final
    applyInitialToggles();
    recalcularTotales();
}
