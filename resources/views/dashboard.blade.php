<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container mt-4 mb-4">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Productos</h5>
                        <a href="{{route('productos.index')}}" class="btn btn-info">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cargos</h5>
                        <a href="{{route('cargos.index')}}" class="btn btn-info">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Puntos</h5>
                        <a href="{{route('puntos.index')}}" class="btn btn-info">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tipo Documentos</h5>
                        <a href="{{route('tipos-documentos.index')}}" class="btn btn-info">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Producciones</h5>
                        <a href="{{route('producciones.index')}}" class="btn btn-info">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Trazabilidad</h5>
                        <a href="{{route('trazabilidad.index')}}" class="btn btn-info">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Orden Producción</h5>
                        <a href="{{route('ordenProduccion.index')}}" class="btn btn-info">Ir</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div></x-app-layout>
