<x-app-layout>
    <div class="container mt-5 mb-4">
        <h2 class="mb-4">Registrar Producción</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Hubo algunos errores:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('producciones.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Nombre de la Producción</label>
                    <input type="text" name="produccion" class="form-control" value="{{ old('produccion') }}" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Tiempo Estimado (minutos)</label>
                    <input type="number" name="tiempo_min" class="form-control" value="{{ old('tiempo_min') }}" min="0" required>
                </div>
            </div>

            <hr>

            <h4>Materias Primas</h4>

            <div id="materias-container">
                @php
                    $materiasOld = old('materias_primas', [null]);
                    $cantidadesOld = old('cantidad', [null]);
                @endphp

                @foreach($materiasOld as $i => $materiaSeleccionada)
                    <div class="row materia-group mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Materia Prima</label>
                            <select name="materias_primas[]" class="form-select" required>
                                <option value="">Seleccione una materia prima</option>
                                @foreach($materiasPrimas as $materiaPrima)
                                    <option value="{{ $materiaPrima->id }}" {{ $materiaSeleccionada == $materiaPrima->id ? 'selected' : '' }}>
                                        {{ $materiaPrima->proNombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cantidad</label>
                            <input type="number" step="0.00001" name="cantidad[]" class="form-control"
                                   value="{{ $cantidadesOld[$i] ?? '' }}" required>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn {{ $loop->first ? 'btn-success add-materia' : 'btn-danger remove-materia' }} w-100">
                                {{ $loop->first ? '+' : '-' }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-md-3 mb-4">
                <button type="submit" class="btn btn-warning w-100">Guardar</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('materias-container');

            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('add-materia')) {
                    const currentGroup = e.target.closest('.materia-group');
                    const newGroup = currentGroup.cloneNode(true);

                    // Limpiar los campos del nuevo grupo
                    newGroup.querySelector('select').value = '';
                    newGroup.querySelector('input[name="cantidad[]"]').value = '';

                    // Cambiar el botón "+" por "-"
                    const newButton = newGroup.querySelector('button');
                    newButton.textContent = '-';
                    newButton.classList.remove('btn-success', 'add-materia');
                    newButton.classList.add('btn-danger', 'remove-materia');

                    container.appendChild(newGroup);
                }

                if (e.target.classList.contains('remove-materia')) {
                    e.target.closest('.materia-group').remove();
                }
            });
        });
    </script>
</x-app-layout>
