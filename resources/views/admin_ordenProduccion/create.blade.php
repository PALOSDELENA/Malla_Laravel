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

        <form action="{{ route('ordenProduccion.store') }}" method="POST">
            @csrf

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
                <div id="materiasPrimasContainer" class="col-md-4 mb-4">
                    <!-- Aquí se generarán los inputs para las materias primas con JS -->
                </div>

                {{-- Cantidad --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Cantidad de Producción</label>
                    <input type="number" step="any" name="cantidad" class="w-full border rounded p-2"
                        value="{{ old('cantidad') }}" required>
                </div>

                {{-- Fecha Inicio --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" class="w-full border rounded p-2"
                        value="{{ old('fecha_inicio') }}" required>
                </div>

                {{-- Fecha Fin --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Fecha de Fin</label>
                    <input type="date" name="fecha_fin" class="w-full border rounded p-2"
                        value="{{ old('fecha_fin') }}">
                </div>

                {{-- Estado --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Estado</label>
                    <input type="text" name="estado" class="w-full border rounded p-2"
                        value="{{ old('estado', 'Pendiente') }}" required>
                </div>

                {{-- Novedad --}}
                <div class="col-md-4 mb-4">
                    <label class="block font-medium">Novedad</label>
                    <input type="text" name="novedadProduccion" class="w-full border rounded p-2"
                        value="{{ old('novedadProduccion') }}">
                </div>
            </div>

            <button type="submit" class="btn btn-info">Guardar Orden</button>
        </form>
        </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectProduccion = document.getElementById('produccionSelect');
        const container = document.getElementById('materiasPrimasContainer');

        selectProduccion.addEventListener('change', function () {
            const produccionId = this.value;
            container.innerHTML = ''; // Limpiar anteriores

            if (!produccionId) return;

            fetch(`/produccion/${produccionId}/materias-primas`)
                .then(response => response.json())
                .then(data => {
                    data.forEach((materia, index) => {
                        const div = document.createElement('div');
                        div.classList.add('mb-2');
                        div.innerHTML = `
                            <label>Materia Prima ${index + 1}: ${materia.nombre} (${materia.unidad})</label>
                            <input type="hidden" name="materias_primas[${index}][producto_id]" value="${materia.id}">
                            <input type="text" name="materias_primas[${index}][cantidad_real]" class="form-control" placeholder="Cantidad consumida" required>
                        `;
                        container.appendChild(div);
                    });
                });
        });
    });
    </script>
</x-app-layout>
