document.addEventListener('DOMContentLoaded', function() {
    cargarPuntos();
    document.getElementById('punto').addEventListener('change', cargarTodasLasSecciones);
    actualizarFechasSemana();
    
    document.getElementById('downloadExcel').addEventListener('click', descargarExcel);
    document.getElementById('semanaHistorica').addEventListener('change', cargarSemanaSeleccionada);
    document.getElementById('btnGuardarHistorico').addEventListener('click', guardarHistorico);
    
    // Agregar botón para limpiar casillas
    agregarBotonLimpiar();
    
    inicializarControlSemanas();
});

function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

function showSavedIndicator() {
    const indicator = document.getElementById('savedIndicator');
    indicator.style.display = 'block';
    setTimeout(() => {
        indicator.style.display = 'none';
    }, 2000);
}

function cargarPuntos() {
    showLoading();
    fetch(URL_PUNTOS)
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {  // Verificar que data sea un array
                const select = document.getElementById('punto');
                select.innerHTML = '<option value="">Seleccione un punto</option>'; // Limpiar y agregar opción por defecto
                
                data.forEach(punto => {
                    const option = document.createElement('option');
                    option.value = punto.id;
                    option.textContent = punto.nombre;
                    select.appendChild(option);
                });
            } else if (data.error) {
                console.error('Error del servidor:', data.error);
                alert('Error al cargar los puntos: ' + data.error);
            } else {
                throw new Error('Formato de respuesta inválido');
            }
        })
        .catch(error => {
            console.error('Error al cargar puntos:', error);
            alert('Error al cargar los puntos. Por favor, recargue la página.');
        })
        .finally(() => {
            hideLoading();
        });
}

function formatFecha(fechaTexto) {
    const partes = fechaTexto.split('/');
    const dia = partes[0].padStart(2, '0');
    const mes = partes[1].padStart(2, '0');
    const anio = partes[2];
    return `${anio}-${mes}-${dia}`;
}

function cargarTodasLasSecciones() {
    const punto = document.getElementById('punto').value;
    if (!punto) return;

    const fechaInicioRaw = document.getElementById('fechaInicio').textContent;
    const fechaFinRaw = document.getElementById('fechaFin').textContent;

    const fechaInicio = formatFecha(fechaInicioRaw);
    const fechaFin = formatFecha(fechaFinRaw);

    showLoading();
    cargarEncargado(punto, fechaInicio, fechaFin);
    cargarSemanasHistoricas(punto);
    
    Promise.all([
        cargarSeccion(punto, 1),
        cargarSeccion(punto, 2),
        cargarSeccion(punto, 3),
        cargarSeccion(punto, 4)  // Nueva sección de almuerzos
    ]).finally(() => {
        hideLoading();
    });
}

function cargarSeccion(punto, seccion) {
    return fetch(`${BASE_URL}/paloteo/reporte/semanal/${punto}/${seccion}`)
        .then(response => response.json())
        .then(data => {
            renderizarTablaInventario(data, seccion);
        })
        .catch(error => {
            console.error(`Error al cargar sección ${seccion}:`, error);
        });
}

function renderizarTablaInventario(data, seccion) {
    const tbody = document.getElementById(`inventarioBody-${seccion}`);
    tbody.innerHTML = '';
    
    data.forEach(producto => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center delete-btn">
                <button type="button" onclick="eliminarProducto(${producto.id})" class="btn">
                    ${producto.nombre}
                </button>
            </td>
            ${generarCeldasDias(producto, seccion)}
        `;
        tbody.appendChild(tr);
    });

    // Fila extra con botón para agregar productos
    const trAdd = document.createElement('tr');
    trAdd.innerHTML = `
        <td colspan="8" class="text-center">
            <button type="button" class="btn btn-sm btn-success" onclick="agregarProducto(${seccion})">
                + Agregar producto
            </button>
        </td>
    `;
    tbody.appendChild(trAdd);
        
    document.querySelectorAll(`#inventarioBody-${seccion} input`).forEach(input => {
        let timeout;
        input.addEventListener('input', function(e) {
            clearTimeout(timeout);
            const valor = e.target.value;
            timeout = setTimeout(() => {
                guardarInventario(e);
            }, 500);
        });

        input.addEventListener('focus', function() {
            this.select();
        });

        input.addEventListener('keydown', function(e) {
            const currentCell = e.target.closest('td');
            const currentRow = currentCell.parentElement;
            const cells = Array.from(currentRow.cells);
            const rows = Array.from(currentRow.parentElement.children);
            const currentRowIndex = rows.indexOf(currentRow);
            const currentCellIndex = cells.indexOf(currentCell);

            switch(e.key) {
                case 'ArrowRight':
                    e.preventDefault();
                    if (currentCellIndex < cells.length - 1) {
                        const nextInput = cells[currentCellIndex + 1].querySelector('input');
                        if (nextInput) nextInput.focus();
                    }
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    if (currentCellIndex > 1) {
                        const prevInput = cells[currentCellIndex - 1].querySelector('input');
                        if (prevInput) prevInput.focus();
                    }
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    if (currentRowIndex > 0) {
                        const upInput = rows[currentRowIndex - 1].cells[currentCellIndex].querySelector('input');
                        if (upInput) upInput.focus();
                    }
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    if (currentRowIndex < rows.length - 1) {
                        const downInput = rows[currentRowIndex + 1].cells[currentCellIndex].querySelector('input');
                        if (downInput) downInput.focus();
                    }
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (currentRowIndex < rows.length - 1) {
                        const downInput = rows[currentRowIndex + 1].cells[currentCellIndex].querySelector('input');
                        if (downInput) downInput.focus();
                    }
                    break;
            }
        });
    });
}

function agregarProducto(seccionId) {
    // Guardamos la sección en un atributo del modal
    const modal = document.getElementById("modalProductos");
    modal.setAttribute("data-seccion-id", seccionId);

    showLoading();
    // Cargar productos (puedes traerlos por AJAX o tenerlos en memoria)
    fetch(BASE_URL + '/paloteo/get/productos') // tu endpoint PHP que devuelve JSON
        .then(res => res.json())
        .then(productos => {
            const tbody = document.getElementById("listaProductos");
            tbody.innerHTML = "";

            productos.forEach(p => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="text-center">${p.nombre}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" onclick="seleccionarProducto(${p.id})">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Mostrar modal (Bootstrap 5)
            const modalBootstrap = new bootstrap.Modal(modal);
            hideLoading();
            modalBootstrap.show();
        })
        .catch(err => console.error("Error cargando productos:", err));
}

function seleccionarProducto(productoId) {
    const modal = document.getElementById("modalProductos");
    const seccionId = modal.getAttribute("data-seccion-id");
    const punto = document.getElementById('punto').value;


    fetch(BASE_URL + '/paloteo/' + productoId + '/productos', {
        method: "PUT",
        headers: { 
            "Content-Type": "application/json",
            "Accept": "application/json",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            producto_id: productoId, 
            seccion_id: seccionId 
        })
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            Swal.fire({
                icon: "success",
                title: resp.message,
                timer: 1500,
                showConfirmButton: false
            });

            // Recargar tabla de inventario de esa sección
            cargarSeccion(punto, seccionId);
        }
    })
    .catch(err => console.error(err));

    // Cerrar modal
    const modalBootstrap = bootstrap.Modal.getInstance(modal);
    modalBootstrap.hide();
}

function eliminarProducto(id) {
    const punto = document.getElementById('punto').value;

    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(BASE_URL + '/api/productos/' + id, {
                method: 'PUT',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Eliminado',
                        data.message,
                        'success'
                    ).then(() => {
                        // location.reload();
                        cargarSeccion(punto, seccionId);
                    });
                } else {
                    Swal.fire(
                        'Error',
                        data.message || 'No se pudo eliminar el producto.',
                        'error'
                    );
                }
            })
            .catch(() => {
                Swal.fire(
                    'Error',
                    'Ocurrió un error en el servidor.',
                    'error'
                );
            });
        }
    });
}

function encontrarSiguienteInput(inputActual) {
    const inputs = Array.from(document.querySelectorAll('input[type="number"]'));
    const index = inputs.indexOf(inputActual);
    return inputs[index + 1];
}

function generarCeldasDias(producto, seccion) {
    const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
    return dias.map(dia => `
        <td>
            <input type="number" 
                   class="form-control" 
                   data-dia="${dia}" 
                   data-producto="${producto.id}" 
                   value="${Math.round(producto[dia])}" 
                   step="1"
                   min="0"
                   onkeypress="return event.charCode >= 48 && event.charCode <= 57">
        </td>
    `).join('');
}

function guardarInventario(event) {
    const input = event.target;
    const producto_id = input.dataset.producto;
    const dia = input.dataset.dia;
    const cantidad = Math.round(input.value);
    const punto_id = document.getElementById('punto').value;

    input.value = cantidad;

    input.style.backgroundColor = '#fff9e6';

    fetch(URL_GUARDAR, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            producto_id,
            punto_id,
            dia,
            cantidad
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            input.style.backgroundColor = '#ffe6e6';
            console.error('Error al guardar:', data.error);
        } else {
            input.style.backgroundColor = '#e6ffe6';
            setTimeout(() => {
                input.style.backgroundColor = '';
            }, 500);
        }
    })
    .catch(error => {
        input.style.backgroundColor = '#ffe6e6';
        console.error('Error:', error);
    });
}

function ordenarPorDia(dia) {
    const tbody = document.getElementById('inventarioBody');
    const rows = Array.from(tbody.getElementsByTagName('tr'));
    
    rows.sort((a, b) => {
        const valueA = parseFloat(a.querySelector(`[data-dia="${dia}"]`).value) || 0;
        const valueB = parseFloat(b.querySelector(`[data-dia="${dia}"]`).value) || 0;
        return valueB - valueA;
    });
    
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}

function actualizarFechasSemana() {
    const today = new Date();
    const monday = new Date(today.setDate(today.getDate() - today.getDay() + 1));
    const sunday = new Date(today.setDate(today.getDate() - today.getDay() + 7));
    
    document.getElementById('fechaInicio').textContent = monday.toLocaleDateString();
    document.getElementById('fechaFin').textContent = sunday.toLocaleDateString();
}
function cargarEncargado(puntoId, fechaInicio, fechaFin) {
    if (!puntoId) return;
    
    fetch(URL_GERENTE + `?punto=${puntoId}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`)
        .then(response => response.json())
        .then(data => {
            const encargadoInput = document.getElementById('encargadoInput');
            const encargadoSection = document.getElementById('encargadoSection');
            
            if (data.success) {
                encargadoInput.value = data.nombre_encargado || '';
                encargadoSection.style.display = 'block';
            } else {
                console.error('Error al cargar encargado:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

function guardarEncargado() {
    const puntoId = document.getElementById('punto').value;
    const nombreEncargado = document.getElementById('encargadoInput').value.trim();
    
    if (!puntoId) {
        alert('Por favor, seleccione un punto primero');
        return;
    }
    
    if (!nombreEncargado) {
        alert('Por favor, ingrese el nombre del encargado');
        return;
    }

    fetch('guardar_encargado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            punto_id: puntoId,
            nombre_encargado: nombreEncargado
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSavedIndicator();
            console.log('Encargado guardado:', data.debug);
        } else {
            alert('Error al guardar el encargado: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar el encargado');
    });
}

function descargarExcel() {
    showLoading();
    
    const fechaInicio = document.getElementById('fechaInicio').textContent;
    const fechaFin = document.getElementById('fechaFin').textContent;
    
fetch(BASE_URL + '/paloteo/export')
    .then(async response => {
        if (!response.ok) {
            // Solo leemos el body aquí si hubo error
            let serverMessage = '';
            try {
                const data = await response.json(); // intenta JSON
                serverMessage = data.message || JSON.stringify(data);
            } catch {
                serverMessage = await response.text(); // fallback a texto
            }
            throw new Error(serverMessage || `Error HTTP ${response.status}`);
        }

        // Aquí solo se llega si todo fue OK, y recién leemos el blob
        return response.blob();
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;

        const currentDate = new Date().toISOString().slice(0, 10);
        a.download = `Paloteo_Semana_${currentDate}.xlsx`;

        document.body.appendChild(a);
        a.click();

        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Error al descargar el Excel:', error);
        alert('Error exacto del servidor:\n' + error.message);
    })
    .finally(() => {
        hideLoading();
    });
}

function inicializarControlSemanas() {
    const punto = document.getElementById('punto').value;
    if (punto) {
        cargarSemanasHistoricas(punto);
    }
}

function cargarSemanasHistoricas(puntoId) {
    fetch(URL_HISTORICO + puntoId)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('semanaHistorica');
            select.innerHTML = '<option value="">Semana actual</option>';
            
            data.forEach(semana => {
                const option = document.createElement('option');
                option.value = semana.id;
                option.textContent = `Semana del ${semana.fecha_inicio} al ${semana.fecha_fin}`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar semanas históricas:', error);
        });
}

function cargarSemanaSeleccionada() {
    const historicoId = document.getElementById('semanaHistorica').value;
    const puntoId = document.getElementById('punto').value;
    
    if (!puntoId) {
        alert('Por favor seleccione un punto primero');
        return;
    }
    
    if (!historicoId) {
        actualizarFechasSemana();
        cargarTodasLasSecciones();
        document.getElementById('btnGuardarHistorico').style.display = 'inline-flex';
        return;
    }
    
    showLoading();
    fetch(URL_CARGAR_HISTORICO + historicoId)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Actualizar fechas mostradas
            document.getElementById('fechaInicio').textContent = data.fecha_inicio;
            document.getElementById('fechaFin').textContent = data.fecha_fin;
            
            // Agrupar productos por sección
            const productosPorSeccion = data.datos.reduce((acc, producto) => {
                if (!acc[producto.seccion_id]) {
                    acc[producto.seccion_id] = [];
                }
                acc[producto.seccion_id].push(producto);
                return acc;
            }, {});
            
            // Renderizar cada sección
            [1, 2, 3].forEach(seccionId => {
                const productos = productosPorSeccion[seccionId] || [];
                renderizarDatosHistoricos(productos, seccionId);
            });
            
            // Ocultar botón de guardar
            document.getElementById('btnGuardarHistorico').style.display = 'none';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar datos históricos: ' + error.message);
        })
        .finally(() => {
            hideLoading();
        });
}

function guardarHistorico() {
    const puntoId = document.getElementById('punto').value;
    if (!puntoId) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor seleccione un punto antes de guardar el histórico',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    const nombreEncargado = document.getElementById('encargadoInput').value.trim();
    if (!nombreEncargado) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo vacío',
            text: 'Por favor, ingrese el nombre del encargado',
            confirmButtonText: 'Ok'
        });
        return;
    }

    // if (!confirm('¿Está seguro de guardar el histórico de esta semana?')) {
    //     return;
    // }

    const fechaInicio = document.getElementById('fechaInicio').textContent;
    const fechaFin = document.getElementById('fechaFin').textContent;

    Swal.fire({
        title: '¿Está seguro?',
        text: "Se guardará el histórico de esta semana",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();

            fetch(URL_SAVE_HISTORICO, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    punto_id: puntoId,
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    encargado: nombreEncargado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Guardado',
                        text: 'El histórico se guardó correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    showSavedIndicator();
                    cargarSemanasHistoricas(puntoId);
                } else {
                    throw new Error(data.error || 'Error al guardar histórico');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar histórico: ' + error.message,
                });
            })
            .finally(() => {
                hideLoading();
            });
        }
    });
}

function renderizarDatosHistoricos(productos, seccionId) {
    const tbody = document.getElementById(`inventarioBody-${seccionId}`);
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    productos.forEach(producto => {
        const cantidades = procesarRegistrosProducto(producto.registros);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${producto.nombre}</td>
            ${generarCeldasHistoricas(cantidades)}
        `;
        tbody.appendChild(tr);
    });
}

function procesarRegistrosProducto(registros) {
    const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
    const cantidades = {};
    
    // Inicializar todos los días con 0
    dias.forEach(dia => cantidades[dia] = 0);
    
    // Procesar registros si existen
    if (Array.isArray(registros)) {
        registros.forEach(registro => {
            if (registro.fecha) {
                const fecha = new Date(registro.fecha);
                const diaSemana = fecha.getDay();
                const dia = dias[diaSemana];
                cantidades[dia] = parseFloat(registro.cantidad) || 0;
            }
        });
    }
    
    return cantidades;
}

function generarCeldasHistoricas(cantidades) {
    const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
    return dias.map(dia => `
        <td>
            <input type="number" 
                   class="form-control historico" 
                   value="${Math.round(cantidades[dia])}"
                   readonly
                   disabled>
        </td>
    `).join('');
}

/**
 * Función para agregar el botón de limpiar casillas dinámicamente
 */
function agregarBotonLimpiar() {
    // Intentar encontrar el contenedor donde poner el botón (cerca del selector de puntos)
    const selectContainer = document.querySelector('.select-container');
    
    if (selectContainer) {
        // Verificar si ya existe un contenedor de acciones
        let actionsContainer = document.querySelector('.actions-container');
        
        if (!actionsContainer) {
            // Crear contenedor de acciones
            actionsContainer = document.createElement('div');
            actionsContainer.className = 'actions-container';
            selectContainer.after(actionsContainer);
        }
        
        // Crear botón de limpiar
        const btnLimpiar = document.createElement('button');
        btnLimpiar.id = 'btnLimpiarCasillas';
        btnLimpiar.className = 'btn-clear-all';
        btnLimpiar.innerHTML = '<i class="fas fa-eraser"></i> Limpiar casillas del punto';
        btnLimpiar.addEventListener('click', limpiarCasillasPorPunto);
        
        // Agregar el botón al contenedor
        actionsContainer.appendChild(btnLimpiar);
    }
}

/**
 * Función para limpiar las casillas del punto seleccionado
 */
function limpiarCasillasPorPunto() {
    const puntoId = document.getElementById('punto').value;
    const puntoNombre = document.getElementById('punto').options[document.getElementById('punto').selectedIndex].text;
    
    if (!puntoId) {
        alert('Por favor, seleccione un punto antes de limpiar las casillas');
        return;
    }
    
    if (!confirm(`¿Está seguro que desea limpiar TODAS las casillas del punto "${puntoNombre}"? \n\nEsta acción pondrá en CERO todos los valores y no se puede deshacer.`)) {
        return;
    }
    
    showLoading();
    
    let totalCasillas = 0;
    let casillasProcesadas = 0;
    let errores = 0;
    
    // Recorrer todas las secciones
    for (let seccion = 1; seccion <= 3; seccion++) {
        const inputs = document.querySelectorAll(`#inventarioBody-${seccion} input[type="number"]:not([disabled])`);
        totalCasillas += inputs.length;
        
        inputs.forEach(input => {
            const producto_id = input.dataset.producto;
            const dia = input.dataset.dia;
            
            // Solo procesar si hay un valor diferente de cero
            if (input.value !== '0') {
                // Cambiar visualmente el valor
                input.value = "0";
                input.style.backgroundColor = '#fff9e6';
                
                // Guardar el nuevo valor en la base de datos
                fetch('guardar_inventario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        producto_id,
                        punto_id: puntoId,
                        dia,
                        cantidad: 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    casillasProcesadas++;
                    
                    if (data.error) {
                        console.error('Error al guardar:', data.error);
                        input.style.backgroundColor = '#ffe6e6';
                        errores++;
                    } else {
                        input.style.backgroundColor = '#e6ffe6';
                        setTimeout(() => {
                            input.style.backgroundColor = '';
                        }, 500);
                    }
                    
                    verificarFinalizacion();
                })
                .catch(error => {
                    casillasProcesadas++;
                    console.error('Error:', error);
                    input.style.backgroundColor = '#ffe6e6';
                    errores++;
                    
                    verificarFinalizacion();
                });
            } else {
                casillasProcesadas++;
                verificarFinalizacion();
            }
        });
    }
    
    function verificarFinalizacion() {
        // Verificar si ya hemos procesado todas las casillas
        if (casillasProcesadas >= totalCasillas) {
            hideLoading();
            showSavedIndicator();
            
            if (errores > 0) {
                alert(`Proceso completado con ${errores} errores. Se limpiaron ${totalCasillas - errores} de ${totalCasillas} casillas.`);
            } else {
                alert(`Se han limpiado todas las casillas para el punto "${puntoNombre}" correctamente.`);
            }
        }
    }
    
    // Si no hay casillas para procesar
    if (totalCasillas === 0) {
        hideLoading();
        alert('No hay datos para limpiar en este punto.');
    }
}
