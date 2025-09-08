<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $perfil = auth()->user()->cargo()->first()->car_nombre ?? 'Sin Cargo';
    @endphp

    <div class="container mt-4 mb-4">
        <div class="row">
            @if ($perfil === 'Administrador')
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{ route('usuarios.index') }}">
                                <i class="fa-solid fa-users fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Usuarios</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{route('cargos.index')}}">
                                <i class="fa-solid fa-user-shield fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Cargos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{route('puntos.index')}}">
                                <i class="fa-solid fa-location-crosshairs fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Puntos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{route('tipos-documentos.index')}}">
                                <i class="fa-solid fa-id-card fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Tipo Documentos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{route('turnos.index')}}">
                                <i class="fa-solid fa-user-clock fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Turnos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{route('kanban.turnos')}}">
                                <i class="fa-solid fa-calendar-days fa-5x icon-hover"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Asignación de Turnos</h5>
                        </div>
                    </div>
                </div>
            @endif
            @if ($perfil === 'Planta' || $perfil === 'Administrador')
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{ route('productos.index') }}">
                                <i class="fa-solid fa-layer-group fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Productos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{ route('productos.stockChart') }}">
                                <i class="fa-solid fa-chart-simple fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Existencias</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{route('producciones.index')}}">
                                <i class="fa-solid fa-industry fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Producciones</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{route('trazabilidad.index')}}">
                                <i class="fa-solid fa-route fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Trazabilidad</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{route('ordenProduccion.index')}}">
                                <i class="fa-solid fa-file-circle-plus fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Orden Producción</h5>
                        </div>
                    </div>
                </div>
            @endif
            @if ($perfil === 'Punto')
                @php
                    $perfil = auth()->user()->cargo()->first()->car_nombre ?? 'Sin Cargo';
                    $punto = auth()->user()->punto()->first()->nombre ?? 'Sin Punto';
                @endphp
                @switch($punto)
                    @case('Administrativo')
                        @php $route = route('dashboardAdmin'); @endphp
                        @break

                    @case('Puente Aranda')
                        @php $route = route('dashboardPuente'); @endphp
                        @break

                    @case('Cafam')
                        @php $route = route('dashboardCafam'); @endphp
                        @break

                    @case('Centro')
                        @php $route = route('dashboardCentro'); @endphp
                        @break

                    @case('Cocina')
                        @php $route = route('dashboardCocina'); @endphp
                        @break

                    @case('Fontibón')
                        @php $route = route('dashboardFon'); @endphp
                        @break

                    @case('Jiménez')
                        @php $route = route('dashboardJim'); @endphp
                        @break

                    @case('Mall Plaza')
                        @php $route = route('dashboardMall'); @endphp
                        @break

                    @case('Multi Plaza')
                        @php $route = route('dashboardPMulti'); @endphp
                        @break

                    @case('Nuestro Bogotá')
                        @php $route = route('dashboardNuestro'); @endphp
                        @break

                    @case('Parrilla')
                        @php $route = route('dashboardParrilla'); @endphp
                        @break

                    @case('Quinta Paredes')
                        @php $route = route('dashboardQuinta'); @endphp
                        @break

                    @case('Salitre Plaza')
                        @php $route = route('dashboardSalitre'); @endphp
                        @break

                    @default
                        @php $route = "#" @endphp
                @endswitch
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="route('paloteo')">
                                <i class="fa-solid fa-layer-group fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Paloteo</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="mt-4 mb-2">
                            <a href="{{$route}}">
                                <i class="fas fa-tachometer-alt fa-5x icon-hover" ></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Dashboard</h5>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div></x-app-layout>
