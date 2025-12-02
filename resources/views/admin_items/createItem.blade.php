<x-app-layout>
    <style>
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 1.5px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.625rem 0.75rem;
            transition: all 0.2s ease-in-out;
            background-color: #ffffff;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            background-color: #ffffff;
        }
        
        .form-control::placeholder {
            color: #adb5bd;
        }
        
        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
        }
        
        .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 0.4rem;
            background-color: #F5C02E !important;
            border: none;
        }
        
        .btn-primary {
            padding: 0.625rem 2rem;
            font-weight: 500;
            border-radius: 0.375rem;
            background-color: #F5C02E;
            border-color: #F5C02E;
        }
        
        .btn-primary:hover {
            background-color: #d4a526;
            border-color: #d4a526;
        }
    </style>
    
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

        <div class="card shadow-sm">
            <div class="card-header"></div>
            <div class="card-body p-4">
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
                        <option value="Carta-E">Entrada</option>
                        <option value="Carta-F">Plato Fuerte</option>
                        <option value="Carta-P">Postre</option>
                        <option value="Carta-B">Bebida</option>
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
                <div class="col-6 mb-4">
                    <label for="proCondicionesConservacion" class="form-label">Condiciones de Conservación</label>
                    <input type="text" class="form-control" id="proCondicionesConservacion" name="proCondicionesConservacion" maxlength="255">
                </div>
                <div class="col-6 mb-4">
                    <label for="proPrecio" class="form-label">Precio</label>
                    <input type="number" class="form-control" id="proPrecio" name="proPrecio" maxlength="255">
                </div>
            </div>

            <div class="row">
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
