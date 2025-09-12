function iniciarSortableKanban() {
    document.querySelectorAll('.kanban-list').forEach(list => {
        if (list.dataset.sortableAttached) return; // evita duplicados

        new Sortable(list, {
            group: 'kanban',
            animation: 150,
            onEnd: function (evt) {
                const turnoId = evt.item.dataset.id;
                const nuevoDia = evt.to.closest('.kanban-column').dataset.day;

                const wireId = evt.to.closest('[wire\\:id]').getAttribute('wire:id');
                const component = Livewire.find(wireId);

                component.call('moverTurno', turnoId, nuevoDia);
            }
        });

        list.dataset.sortableAttached = true;
    });
}

document.addEventListener('livewire:load', iniciarSortableKanban);
document.addEventListener('livewire:navigated', iniciarSortableKanban);
document.addEventListener('livewire:updated', iniciarSortableKanban);


// Tags con Tagify para el input de ingredientes
document.addEventListener('DOMContentLoaded', function () {
    // Selecciona el input
    var input = document.querySelector('#proListaIngredientes');
    
    // Inicializa Tagify
    // new Tagify(input, {
    //     delimiters: ",|Enter",
    //     dropdown: {
    //         enabled: 0,
    //         maxItems: 10,
    //         classname: "tags-look",
    //         closeOnSelect: false
    //     }
    // });

    new Tagify(input, {
        delimiters: ",|Enter",
        enforceWhitelist: false,
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
    });

});

// Eliminar Producto
document.addEventListener('DOMContentLoaded', function () {
    const botonesEliminar = document.querySelectorAll('.btn-eliminar');
    const formEliminar = document.getElementById('form-eliminar');

    botonesEliminar.forEach(boton => {
        boton.addEventListener('click', function () {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const url = this.dataset.url.replace('__ID__', id);

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Se eliminará el producto "${nombre}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    formEliminar.setAttribute('action', url);
                    formEliminar.submit();
                }
            });
        });
    });
});

// Llenar Modal de Edición de Producto
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editarProductoModal');
    const form = document.getElementById('formEditarProducto');

    document.querySelectorAll('.btn-editar').forEach(boton => {
        boton.addEventListener('click', () => {
            const id = boton.dataset.id;

            form.setAttribute('action', `/productos/${id}`);

            document.getElementById('producto_id').value = id;
            document.getElementById('modal_proNombre').value = boton.dataset.nombre;
            document.getElementById('modal_proUnidadMedida').value = boton.dataset.unidad;
            document.getElementById('modal_proTipo').value = boton.dataset.tipo;
            document.getElementById('modal_proListaIngredientes').value = boton.dataset.ingredientes;
            document.getElementById('modal_proCondicionesConservacion').value = boton.dataset.condiciones;
            document.getElementById('modal_proFabricante').value = boton.dataset.fabricante;
            document.getElementById('modal_proPrecio').value = Number(boton.dataset.precio);
            document.getElementById('modal_proSeccion').value = boton.dataset.seccion;
        });
    });
});

// Eliminar Cargo
function confirmDelete(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡Esta acción no se puede deshacer!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
};

// Mover Turnos en Kanban
// function iniciarSortable() {
//     document.querySelectorAll('.kanban-list').forEach(list => {
//         new Sortable(list, {
//             group: 'kanban',
//             animation: 150,
//             onEnd: function (evt) {
//                 const turnoId = evt.item.dataset.id;
//                 const nuevoDia = evt.to.closest('.kanban-column').dataset.day;

//                 const component = Livewire.find(evt.from.closest('[wire\\:id]').getAttribute('wire:id'));
//                 component.call('moverTurno', turnoId, nuevoDia);
//             },
//             onStart: function (evt) {
//                 console.log("Arrastrando", evt.item.dataset.id);
//             }
//         });
//     });
// }

// Inicial cuando carga la página
// document.addEventListener('livewire:load', () => {
//     iniciarSortable();
// });

// Re-inicializa después de cualquier actualización Livewire
// document.addEventListener('livewire:update', () => {
//     iniciarSortable();
// });

document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const inventario = row.querySelector('[name*="[inventario]"]');
        const cantidadBodega = row.querySelector('td:nth-child(4)');
        const sugerido = row.querySelector('[name*="[sugerido]"]');
        const pedido1 = row.querySelector('[name*="[pedido_1]"]');
        const pedido2 = row.querySelector('[name*="[pedido_2]"]');
        const totalPedido = row.querySelector('[name*="[total_pedido]"]');
        const precioTotal = row.querySelector('[name*="[precio_total]"]');
        const costoUnitario = parseFloat(row.querySelector('td:nth-child(10)').textContent.trim());
        const selectProducto = row.querySelector('select[name*="[producto_id]"]');

        function calcular() {
            const inv = parseFloat(inventario.value) || 0;
            const bodega = parseFloat(cantidadBodega.textContent) || 0;
            const p1 = parseFloat(pedido1.value) || 0;
            const p2 = parseFloat(pedido2.value) || 0;

            const sug = Math.max(0, bodega - inv);
            const total = p1 + p2;
            const totalPrecio = total * costoUnitario;

            sugerido.value = sug;
            totalPedido.value = total;
            precioTotal.value = totalPrecio.toFixed(2);
        }

        [inventario, pedido1, pedido2].forEach(input => {
            input.addEventListener('input', calcular);
        });

        calcular(); // Cálculo inicial
    });

    // Evitar enviar productos vacíos
    // Evitar enviar productos vacíos
    const form = document.querySelector('form[method="POST"]');

    form.addEventListener('submit', (e) => {
        let hayProductosValidos = false;

        document.querySelectorAll('tbody tr').forEach(row => {
            const pedido1 = row.querySelector('[name*="[pedido_1]"]');
            const pedido2 = row.querySelector('[name*="[pedido_2]"]');

            const p1 = parseFloat(pedido1.value) || 0;
            const p2 = parseFloat(pedido2.value) || 0;

            if (p1 > 0 || p2 > 0) {
                hayProductosValidos = true;
            } else {
                // Quitar el name de los inputs vacíos para que no viajen
                row.querySelectorAll('input, select, textarea').forEach(input => {
                    input.removeAttribute('name');
                });
            }
        });

        if (!hayProductosValidos) {
            e.preventDefault();
            alert("Debes ingresar al menos un producto con pedido para guardar la orden.");
        }
    });    
    // Cargar datos guardados al iniciar
    function cargarDatosGuardados() {
        const datosGuardados = JSON.parse(localStorage.getItem('ordenCompraTemp') || '{}');
        
        rows.forEach(row => {
            const productoId = row.querySelector('[name*="[producto_nombre]"]').name.match(/\[(\d+)\]/)[1];
            if (datosGuardados[productoId]) {
                const datos = datosGuardados[productoId];
                row.querySelector('[name*="[inventario]"]').value = datos.inventario || '';
                row.querySelector('[name*="[pedido_1]"]').value = datos.pedido_1 || '';
                row.querySelector('[name*="[pedido_2]"]').value = datos.pedido_2 || '';
                row.querySelector('[name*="[observaciones]"]').value = datos.observaciones || '';
                
                // Recalcular valores
                const event = new Event('input');
                row.querySelector('[name*="[inventario]"]').dispatchEvent(event);
            }
        });
    }

    // Guardar datos cuando cambian
    function guardarDatos(row) {
        const datosGuardados = JSON.parse(localStorage.getItem('ordenCompraTemp') || '{}');
        const productoId = row.querySelector('[name*="[producto_nombre]"]').name.match(/\[(\d+)\]/)[1];
        
        datosGuardados[productoId] = {
            inventario: row.querySelector('[name*="[inventario]"]').value,
            pedido_1: row.querySelector('[name*="[pedido_1]"]').value,
            pedido_2: row.querySelector('[name*="[pedido_2]"]').value,
            observaciones: row.querySelector('[name*="[observaciones]"]').value
        };

        localStorage.setItem('ordenCompraTemp', JSON.stringify(datosGuardados));
    }

    // Agregar event listeners para guardar datos
    rows.forEach(row => {
        const inputs = row.querySelectorAll('input:not([readonly])');
        inputs.forEach(input => {
            input.addEventListener('change', () => guardarDatos(row));
        });
    });

    // Limpiar localStorage cuando se envía el formulario
    form.addEventListener('submit', () => {
        if (!form.querySelector('[name="proveedor"]')) { // Solo si es el formulario de guardado
            localStorage.removeItem('ordenCompraTemp');
        }
    });

    // Cargar datos guardados al iniciar
    cargarDatosGuardados();
});


