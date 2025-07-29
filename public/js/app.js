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

