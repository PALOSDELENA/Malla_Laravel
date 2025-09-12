<x-app-layout>
    @php
        $perfil = auth()->user()->cargo()->first()->car_nombre ?? 'Sin Cargo';
    @endphp
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="container">
    <h1>Órdenes de Compra </h1>
    
    <!-- Filtros de fecha -->
    <form method="GET" class="filter-container">
        <div class="filter-group">
            <label>Desde:</label>
            <input type="date" name="fecha_inicio"  class="form-control">
        </div>
        <div class="filter-group">
            <label>Hasta:</label>
            <input type="date" name="fecha_fin"  class="form-control">
        </div>
        <div class="filter-buttons">
            <button type="submit" class="button">Filtrar</button>
            <a href="#" class="button">Limpiar Filtros</a>
        </div>
    </form>

    <div class="actions-container">
        <a href="#" class="button">Volver al Inicio</a>
            <a href="{{route('crearOrden')}}" class="button">Generar Nueva Orden</a>
    </div>

    @if (request('mensaje') === 'orden_eliminada')
        <div style="background-color: #4CAF50; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            La orden ha sido eliminada exitosamente.
        </div>
    @endif
    
    @if ($ordenes->isEmpty())
        <p class="no-results">No se encontraron órdenes para el período seleccionado.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Responsable</th>
                    <th>Correo Electrónico</th>
                    <th>Fecha Entrega 1</th>
                    <th>Fecha Entrega 2</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordenes as $orden)
                    <tr>
                        <td>{{ $orden->id }}</td>
                        <td>{{ $orden->responsable }}</td>
                        <td>{{ $orden->email }}</td>
                        <td>{{ $orden->fecha_entrega_1 }}</td>
                        <td>{{ $orden->fecha_entrega_2 }}</td>
                        <td>
                            <span class="estado-{{ $orden->estado }}">
                                {{ ucfirst($orden->estado ?? 'pendiente') }}
                            </span>
                        </td>
                        <td>
                            <a href="#" class="button">Ver</a>

                            @if ($perfil == 'Administrador')
                                @if ($orden->estado === 'pendiente')
                                    <a href="{{ route('ordenes.revisar', $orden->id) }}" class="button button-review">Revisar</a>
                                @endif
                                <a href="#" class="button button-edit">Editar</a>
                            @endif

                            @if ($perfil || $orden->estado === 'pendiente')
                                <a href="#" 
                                   onclick="return confirm('¿Seguro que deseas eliminar esta orden?')"
                                   class="button">Eliminar</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Paginación -->
        {{ $ordenes->links() }}
    @endif
    </div>
</x-app-layout>
