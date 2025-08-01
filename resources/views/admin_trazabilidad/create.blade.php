<x-app-layout>
    <div class="container mt-5 mb-4">
        <h2 class="mb-4">Registrar Movimiento</h2>

        <form action="{{ route('trazabilidad.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="mb-3 col-md-4">
                    <label class="form-label">Fecha de Movimiento</label>
                    <input type="date" name="traFechaMovimiento" class="form-control" required>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Tipo de Movimiento</label>
                    <select name="traTipoMovimiento" class="form-select" required>
                        <option value="Ingreso">Ingreso</option>
                        <option value="Egreso">Egreso</option>
                        <option value="Devolución">Devolución</option>
                    </select>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Producto</label>
                    <select name="traIdProducto" class="form-select" required>
                        <option value="">Seleccione un producto</option>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}">{{ $producto->proNombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="traCantidad" class="form-control" step="any" required>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Lote/Serie</label>
                    <input type="text" name="traLoteSerie" class="form-control" required maxlength="255">
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Observaciones</label>
                    <input type="text" name="traObservaciones" class="form-control" maxlength="500">
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Almacén/Ubicación</label>
                    <select name="traDestino" class="form-select" required>
                        <option value="Puente Aranda">Puente Aranda</option>
                    </select>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Responsable</label>
                    <select name="traResponsable" class="form-select" required>
                        <option value="">Seleccione un responsable</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->num_doc }}">{{ $usuario->usu_nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Color del Producto</label>
                    <select name="traColor" class="form-select" required>
                        <option value="Bueno">Bueno</option>
                        <option value="Malo">Malo</option>
                    </select>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Textura del Producto</label>
                    <select name="traTextura" class="form-select" required>
                        <option value="Bueno">Bueno</option>
                        <option value="Malo">Malo</option>
                    </select>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Olor del Producto</label>
                    <select name="traOlor" class="form-select" required>
                        <option value="Bueno">Bueno</option>
                        <option value="Malo">Malo</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning w-100">Registrar Movimiento</button>
            </div>
        </form>
    </div>
</x-app-layout>
