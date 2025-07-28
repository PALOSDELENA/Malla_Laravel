<x-app-layout>
    <div class="container py-6 px-4">
        <a href="{{ route('ordenProduccion.create') }}" class="btn btn-primary mb-4">Nueva Orden</a>

        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Responsable</th>
                    <th>Producción</th>
                    <th>Estado</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordenes as $orden)
                    <tr>
                        <td>{{ $orden->id }}</td>
                        <td>{{ $orden->responsable1->usu_nombre ?? 'N/A' }}</td>
                        <td>{{ $orden->producciones->produccion ?? 'N/A' }}</td>
                        <td>{{ $orden->estado }}</td>
                        <td>{{ $orden->fecha_inicio }}</td>
                        <td>{{ $orden->fecha_fin ?? '-' }}</td>
                        <td>
                            <a href="{{ route('ordenProduccion.update', $orden->id) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('ordenProduccion.destroy', $orden->id) }}" method="POST" style="display:inline-block">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta orden?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
