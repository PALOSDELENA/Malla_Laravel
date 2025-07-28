<x-app-layout>
    <div class="container mt-4">
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
        @endif
        <h2 class="mb-4">Registrar Producto</h2>

        <form action="{{ route('productos.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Nombre del Producto -->
                <div class="col-md-6 mb-3">
                    <label for="proNombre" class="form-label">Nombre del Producto</label>
                    <input type="text" class="form-control" id="proNombre" name="proNombre" required maxlength="255">
                </div>

                <!-- Unidad de Medida -->
                <div class="col-md-6 mb-3">
                    <label for="proUnidadMedida" class="form-label">Unidad de Medida</label>
                    <select class="form-select" id="proUnidadMedida" name="proUnidadMedida" required>
                        <option value="" selected disabled>Seleccione una unidad</option>
                        <option value="Kilogramo">Kilogramo</option>
                        <option value="Gramo" selected>Gramo</option>
                        <option value="Litro">Litro</option>
                        <option value="Unidad">Unidad</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Tipo de Producto -->
                <div class="col-md-6 mb-3">
                    <label for="proTipo" class="form-label">Tipo de Producto</label>
                    <select class="form-select" id="proTipo" name="proTipo" required>
                        <option value="" selected disabled>Seleccione el tipo</option>
                        <option value="Materia Prima">Materia Prima</option>
                        <option value="Producto Terminado">Producto Terminado</option>
                    </select>
                </div>

                <!-- Fabricante -->
                <div class="col-md-6 mb-3">
                    <label for="proFabricante" class="form-label">Fabricante</label>
                    <input type="text" class="form-control" id="proFabricante" name="proFabricante" value="Palos de Leña" maxlength="255">
                </div>
            </div>

            <div class="row">
                <!-- Ingredientes -->
                <div class="col-12 mb-3">
                    <label for="proListaIngredientes" class="form-label">Ingredientes</label>
                    <input type="text" class="form-control" id="proListaIngredientes" name="proListaIngredientes" placeholder="Ej: Harina, Azúcar, Sal">
                    <div class="form-text">Presiona Enter o Coma para agregar cada ingrediente.</div>
                </div>
            </div>

            <div class="row">
                <!-- Condiciones de Conservación -->
                <div class="col-12 mb-4">
                    <label for="proCondicionesConservacion" class="form-label">Condiciones de Conservación</label>
                    <input type="text" class="form-control" id="proCondicionesConservacion" name="proCondicionesConservacion" maxlength="255">
                </div>
            </div>

            <div class="row">
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
