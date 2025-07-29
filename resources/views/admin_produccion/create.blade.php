<x-app-layout>
    <div class="container mt-5 mb-4">
        <h2 class="mb-4">Registrar Producción</h2>

        <form action="{{ route('producciones.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="produccion" class="form-label">Nombre de la Producción</label>
                    <input type="text" name="produccion" id="produccion" class="form-control" required maxlength="255" value="{{ old('produccion') }}">
                    @error('produccion')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 mb-4">
                    <label for="tiempo_min" class="form-label">Tiempo Estimado (en minutos)</label>
                    <input type="number" name="tiempo_min" id="tiempo_min" class="form-control" required min="0" value="{{ old('tiempo_min') }}">
                    @error('tiempo_min')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
            </div>

            <div id="materias-container">
                <div class="row materia-group mb-3">
                    <div class="col-md-10">
                        <label class="form-label">Materia Prima</label>
                        <select name="materias_primas[]" class="form-select">
                            <option value="">Seleccione una materia prima</option>
                            @foreach($materiasPrimas as $materiaPrima)
                                <option value="{{ $materiaPrima->id }}">{{ $materiaPrima->proNombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-success add-materia w-100">+</button>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary w-100">Guardar</button>
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

                    // Limpiar el select en el nuevo grupo
                    newGroup.querySelector('select').value = '';

                    // Cambiar el botón "+" por un botón de eliminar "-"
                    const newButton = newGroup.querySelector('button');
                    newButton.textContent = '-';
                    newButton.classList.remove('btn-success', 'add-materia');
                    newButton.classList.add('btn-danger', 'remove-materia');

                    container.appendChild(newGroup);
                }

                if (e.target.classList.contains('remove-materia')) {
                    const groupToRemove = e.target.closest('.materia-group');
                    groupToRemove.remove();
                }
            });
        });
    </script>
</x-app-layout>
