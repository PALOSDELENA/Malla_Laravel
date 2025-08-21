document.addEventListener('DOMContentLoaded', function() {
    // cargarProductos();
    
    const productoModal = document.getElementById('productoModal');
    const deleteModal = document.getElementById('deleteModal');
    const closeButtons = document.querySelectorAll('.close');
    
    document.getElementById('btnNuevoProducto').addEventListener('click', () => {
        document.getElementById('modalTitle').textContent = 'Nuevo Producto';
        document.getElementById('productoForm').reset();
        document.getElementById('producto_id').value = '0';
        productoModal.style.display = 'block';
    });
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    window.addEventListener('click', event => {
        if (event.target === productoModal) {
            productoModal.style.display = 'none';
        }
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
    });
    
    document.getElementById('btnCancelarEliminar').addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });
    
    document.getElementById('productoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        guardarProducto();
    });
});

function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

function showSavedIndicator(mensaje = 'Operación completada exitosamente') {
    const indicator = document.getElementById('savedIndicator');
    indicator.textContent = mensaje;
    indicator.style.display = 'block';
    setTimeout(() => {
        indicator.style.display = 'none';
    }, 2000);
}

// function cargarProductos() {
//     showLoading();
//     fetch(`${URL}`)
//         .then(response => {
//             console.log("Status:", response.status);
//             return response.json(); // ✅ parsear directamente como JSON
//         })
//         .then(data => {
//             console.log("Respuesta JSON:", data);
//             renderizarProductos(data); // ✅ aquí ya recibes un array de objetos
//         })
//         .catch(error => {
//             console.error("Error al cargar productos:", error);
//             document.getElementsByClassName('productosBody').innerHTML = 
//                 `<tr><td colspan="4" style="text-align:center; color:red;">
//                     Error al cargar la lista de productos: ${error.message}
//                 </td></tr>`;
//         })
//         .finally(() => {
//             hideLoading();
//         });
// }
// function renderizarProductos(productos) {
//     const tbody = document.getElementsByClassName('productosBody');
//     tbody.innerHTML = '';
    
//     if (!productos || productos.length === 0) {
//         tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; font-style:italic;">
//             No hay productos registrados. Use el botón "Nuevo Producto" para añadir uno.
//         </td></tr>`;
//         return;
//     }
    
//     productos.forEach(producto => {
//         const tr = document.createElement('tr');
//         tr.innerHTML = `
//             <td style="text-align:center;">${producto.id}</td>
//             <td>${producto.nombre}</td>
//             <td>${producto.seccion_nombre || ''}</td>
//             <td class="action-buttons">
//                 <button class="btn-warning" title="Editar ${producto.nombre}" onclick="editarProducto(${producto.id})">
//                     <i class="fas fa-edit"></i>
//                 </button>
//                 <button class="btn-danger" title="Eliminar ${producto.nombre}" onclick="confirmarEliminar(${producto.id})">
//                     <i class="fas fa-trash-alt"></i>
//                 </button>
//             </td>
//         `;
//         tbody.appendChild(tr);
//     });
// }

function editarProducto(id) {
    showLoading();
    fetch(`api/productos_obtener.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            document.getElementById('producto_id').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('seccion_id').value = data.seccion_id;
            
            document.getElementById('modalTitle').textContent = 'Editar Producto';
            document.getElementById('productoModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error al obtener datos del producto:', error);
            alert('Error al cargar los datos del producto: ' + error.message);
        })
        .finally(() => {
            hideLoading();
        });
}

function guardarProducto() {
    const form = document.getElementById('productoForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = {
        id: document.getElementById('producto_id').value,
        nombre: document.getElementById('nombre').value.trim(),
        seccion_id: document.getElementById('seccion_id').value
    };
    
    if (!formData.nombre) {
        alert('El nombre del producto no puede estar vacío');
        return;
    }
    
    showLoading();
    fetch('api/productos_guardar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        
        document.getElementById('productoModal').style.display = 'none';
        cargarProductos();
        showSavedIndicator(data.mensaje || 'Producto guardado correctamente');
    })
    .catch(error => {
        console.error('Error al guardar producto:', error);
        alert('Error al guardar el producto: ' + error.message);
    })
    .finally(() => {
        hideLoading();
    });
}

function confirmarEliminar(id) {
    showLoading();
    fetch(`api/productos_obtener.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            const btnConfirmar = document.getElementById('btnConfirmarEliminar');
            btnConfirmar.dataset.productoId = id;
            btnConfirmar.dataset.productoNombre = data.nombre;
            
            const newBtn = btnConfirmar.cloneNode(true);
            btnConfirmar.parentNode.replaceChild(newBtn, btnConfirmar);
            
            newBtn.addEventListener('click', function() {
                const idAEliminar = this.dataset.productoId;
                const nombreAEliminar = this.dataset.productoNombre;
                eliminarProducto(idAEliminar, nombreAEliminar);
            });
            
            document.getElementById('deleteModal').style.display = 'block';
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('Error al cargar datos del producto');
        });
}

function eliminarProducto(id, nombre) {
    showLoading();
    
    fetch(`api/productos_eliminar.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('deleteModal').style.display = 'none';
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            cargarProductos();
            showSavedIndicator(`Producto "${nombre}" eliminado correctamente`);
        })
        .catch(error => {
            console.error('Error al eliminar producto:', error);
            alert('Error al eliminar el producto: ' + error.message);
        })
        .finally(() => {
            hideLoading();
        });
}
