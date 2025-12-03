<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <!-- <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" /> -->
                        <img src="{{ asset('img/anteojos.ico') }}" alt="Logo-Palos-de-Leña" class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex navbar">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <!-- <x-nav-link :href="route('filament.admin.pages.dashboard')">
                        {{ __('Ir a Panel de Administración') }}
                    </x-nav-link> -->
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
                            @php $route = route('dashboardMulti'); @endphp
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

                        @case('Hayuelos')
                            @php $route = route('dashboardHayuelos'); @endphp
                            @break

                        @default
                            @php $route = "#" @endphp
                    @endswitch
                        <x-nav-link :href="$route" class="header-btn">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="tooltip">Ver Dashboard</span>
                        </x-nav-link>
                    @if ($perfil === 'Administrador' || $perfil === 'Punto')
                        <!-- <x-nav-link :href="route('dashboard')" class="header-btn">
                            <i class="fas fa-chart-line"></i>
                            <span class="tooltip">Ver Métricas</span>
                        </x-nav-link> -->
                        <x-nav-link :href="route('paloteo')" class="header-btn">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="tooltip">Ir al Paloteo</span>
                        </x-nav-link>
                        <!-- <x-nav-link :href="route('dashboard')" class="header-btn">
                            <i class="fas fa-list"></i>
                            <span class="tooltip">Ver Lista Completa</span>
                        </x-nav-link> -->
                        <!-- <x-nav-link href="{{asset('/admin/turnos-kanban-board-page')}}" class="header-btn">
                            <i class="fas fa-piggy-bank"></i>
                            <span class="tooltip">Ir a Formularios</span>
                        </x-nav-link>-->
                    @endif 

                    @if ($perfil === 'Administrador')
                        <x-nav-link :href="route('usuarios.index')" class="header-btn">
                            <i class="fa-solid fa-users fa-5x" ></i>
                            <span class="tooltip">Usuarios</span>
                        </x-nav-link>
                        <x-nav-link href="https://172.16.10.184/" class="header-btn">
                            <i class="fa-solid fa-phone-volume a-5x"></i>
                            <span class="tooltip">PBX</span>
                        </x-nav-link>
                    @endif 

                    @if ($perfil === 'Planta')
                        <x-nav-link :href="route('productos.index')" class="header-btn">
                            <i class="fa-solid fa-layer-group fa-5x icon-hover-nav" ></i>
                            <span class="tooltip">Productos</span>
                        </x-nav-link>
                        <x-nav-link :href="route('productos.stockChart')" class="header-btn">
                            <i class="fa-solid fa-chart-simple fa-5x icon-hover-nav" ></i>
                            <span class="tooltip">Existencias</span>
                        </x-nav-link>
                        <x-nav-link :href="route('producciones.index')" class="header-btn">
                            <i class="fa-solid fa-industry fa-5x icon-hover-nav" ></i>
                            <span class="tooltip">Producciones</span>
                        </x-nav-link>
                        <x-nav-link :href="route('trazabilidad.index')" class="header-btn">
                            <i class="fa-solid fa-route fa-5x icon-hover-nav" ></i>
                            <span class="tooltip">Trazabilidad</span>
                        </x-nav-link>
                        <x-nav-link :href="route('ordenProduccion.index')" class="header-btn">
                            <i class="fa-solid fa-file-circle-plus fa-5x icon-hover-nav" ></i>
                            <span class="tooltip">Orden Producción</span>
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->usu_nombre }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link> -->

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <!-- <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link> -->
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link> -->

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
