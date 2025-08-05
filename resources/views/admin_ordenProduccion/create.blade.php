<x-app-layout>
    <div class="container py-6 px-4">
        <h3 class="text-2xl font-bold mb-4">Crear Orden de Producción</h3>
        @if ($errors->has('error'))
            <div class="alert alert-danger">
                {{ $errors->first('error') }}
            </div>
        @endif
        @if ($errors->has('materias_primas'))
            <div class="alert alert-danger">
                {{ $errors->first('materias_primas') }}
            </div>
        @endif
        <div class="alert alert-info">
            Por favor, asegúrese de que las materias primas requeridas cuentan con suficiente stock. De lo contrario, la orden no podrá ser procesada. 
        </div>

        <form action="{{ route('ordenProduccion.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="row">
                {{-- Responsable --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Responsable</label>
                    <select name="responsable" class="w-full border rounded p-2">
                        <option value="">Seleccione un responsable</option>
                        @foreach($responsables as $doc => $nombre)
                            <option value="{{ $doc }}" {{ old('responsable') == $doc ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fecha Inicio --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Fecha de Inicio</label>
                    <input type="date" id="fecha-inicio" name="fecha_inicio" class="w-full border rounded p-2"
                        value="{{ old('fecha_inicio') }}" required>
                </div>

                {{-- Fecha Fin --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Fecha de Fin</label>
                    <input type="date" name="fecha_fin" class="w-full border rounded p-2"
                        value="{{ old('fecha_fin') }}">
                </div>

                {{-- Cantidad --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Cantidad de Producción</label>
                    <input type="number" step="any" id="cantidadBase" name="cantidad" class="w-full border rounded p-2"
                        value="{{ old('cantidad') }}" required>
                </div>
                
                {{-- Estado --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Estado</label>
                    <select name="estado" class="form-select"
                        value="{{ old('estado', 'Pendiente') }}" required>
                        <option value="Pendiente" {{ old('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="En Proceso" {{ old('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="Finalizada" {{ old('estado') == 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="Cancelada" {{ old('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                {{-- Novedad --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Novedad</label>
                    <input type="text" name="novedadProduccion" class="w-full border rounded p-2"
                        value="{{ old('novedadProduccion') }}">
                </div>

                {{-- Producción --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Producción</label>
                    <select id="produccionSelect" name="produccion_id" class="form-select">
                        <option value="">Seleccione una producción</option>
                        @foreach ($producciones as $produccion)
                            <option value="{{ $produccion->id }}" {{ old('produccion_id') == $produccion->id ? 'selected' : '' }}>
                                {{ $produccion->produccion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Materias primas (contenedor dinámico) --}}
                <div id="materiasPrimasContainer" class="row col-md-12 mb-4">
                    <!-- Aquí se generarán los inputs para las materias primas con JS -->
                </div>
            </div>

            <button type="submit" class="btn btn-warning">Guardar Orden</button>
        </form>
        </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const inputFecha = document.getElementById('fecha-inicio');
            if (!inputFecha.value) { // solo si está vacío
                const hoy = new Date();
                const yyyy = hoy.getFullYear();
                const mm = String(hoy.getMonth() + 1).padStart(2, '0');
                const dd = String(hoy.getDate()).padStart(2, '0');
                const fechaActual = `${yyyy}-${mm}-${dd}`;
                inputFecha.value = fechaActual;
            }
        });
        document.addEventListener("DOMContentLoaded", function () {
        const selectProduccion = document.getElementById('produccionSelect');
        const container = document.getElementById('materiasPrimasContainer');
        const cantidadBaseInput = document.getElementById('cantidadBase');

        selectProduccion.addEventListener('change', function () {
            const produccionId = this.value;
            container.innerHTML = ''; // Limpiar campos anteriores

            if (!produccionId) return;

            fetch(`/produccion/${produccionId}/materias-primas`)
                .then(response => response.json())
                .then(data => {
                    data.forEach((materia, index) => {
                        const row = document.createElement('div');
                        row.classList.add('row', 'mb-2', 'align-items-end');

                        // Materia prima info + cantidad requerida
                        const divMateria = document.createElement('div');
                        divMateria.classList.add('col-md-6');
                        divMateria.innerHTML = `
                            <label><small>M.P ${index + 1}: ${materia.nombre} (${materia.unidad})</small></label>
                            <input type="text" 
                                value="${materia.cantidad}" 
                                class="form-control cantidad-real" 
                                readonly>
                            <input type="hidden" 
                                name="materias_primas[${index}][producto_id]" 
                                value="${materia.id}">
                        `;

                        // Cantidad a consumir (calculada dinámicamente)
                        const divCalculada = document.createElement('div');
                        divCalculada.classList.add('col-md-6');
                        divCalculada.innerHTML = `
                            <label><small>Cantidad total a consumir</small></label>
                            <input type="number" 
                                step="0.001"
                                name="materias_primas[${index}][cantidad_consumida]" 
                                class="form-control cantidad-calculada" 
                                placeholder="Calculado automáticamente" 
                                readonly>
                        `;

                        // Agregar al contenedor
                        row.appendChild(divMateria);
                        row.appendChild(divCalculada);
                        container.appendChild(row);
                    });

                    // Activar cálculo automático si existe un input base
                    if (cantidadBaseInput) {
                        cantidadBaseInput.addEventListener('input', () => {
                            const base = parseFloat(cantidadBaseInput.value) || 0;

                            document.querySelectorAll('.cantidad-real').forEach((inputReal, index) => {
                                const cantidadRequerida = parseFloat(inputReal.value) || 0;
                                const cantidadCalculada = cantidadRequerida * base;

                                const inputCalculada = document.querySelectorAll('.cantidad-calculada')[index];
                                inputCalculada.value = cantidadCalculada.toFixed(3);
                            });
                        });
                    }
                });
        });
    });
    </script>
</x-app-layout>
