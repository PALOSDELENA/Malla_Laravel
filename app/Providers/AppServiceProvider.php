<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (config('app.env') === 'production') {
                URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $routeName = Route::currentRouteName();
            $titles = [
                //Titulos de vistas por nombre de rutas:
                'dashboard' => 'Home',
                'users.index' => 'Usuarios',
                'reports.index' => 'Reportes',
                'profile.edit' => 'Profile',
                'productos.index' => 'Productos',
                'ordenProduccion.index' => 'Ordenes de Producción',
                'ordenProduccion.create' => 'Crear Orden de Producción',
                'cargos.index' => 'Cargos',
                'puntos.index' => 'Puntos',
                'turnos.index' => 'Turnos',
                'usuarios.index' => 'Usuarios',
                'usuarios.create' => 'Crear Usuario',
                'tipos-documentos.index' => 'Tipos de Documento',
                'kanban.turnos' => 'Kanban Turnos',
                'producciones.index' => 'Producciones',
                'producciones.create' => 'Crear Producción',
                'trazabilidad.index' => 'Trazabilidad',
                'trazabilidad.create' => 'Registrar Movimiento',
                'productos.stockChart' => 'Existencias de Productos',
                'dashboardAdmin' => 'Dashboard Palos de Leña',
                'dashboardPuente' => 'Dashboard Puente Aranda',
                'dashboardCafam' => 'Dashboard Cafam',
                'dashboardCentro' => 'Dashboard Centro',
                'dashboardCocina' => 'Dashboard Cocina',
                'dashboardFon' => 'Dashboard Fontibón',
                'dashboardJim' => 'Dashboard Jimenénez',
                'dashboardMall' => 'Dashboard Mall Plaza',
                'dashboardMulti' => 'Dashboard Multi Plaza',
                'dashboardNuestro' => 'Dashboard Nuestro Bogotá',
                'dashboardParrilla' => 'Dashboard Parrilla',
                'dashboardQuinta' => 'Dashboard Quinta Paredes',
                'dashboardSalitre' => 'Dashboard Salitre',
                'dashboardHayuelos' => 'Dashboard Hayuelos',
                'paloteo' => 'Paloteo',
                'ordenCompra' => 'Órdenes de Compra',
                'crearOrden' => 'Generar Orden de Compra',
                'editOrden' => 'Editar Orden',
                'informe.index' => 'Informes',
                'novedad.index' => 'Reporte de Novedades',
                'nov.proveedor.index' => 'Novedades de Proveedores',
                'coti.index' => 'Abemus Cotización',
                'coti.create' => 'Crear Cotización',
                'coti.show' => 'Ver Cotización',
            ];

            $view->with('pageTitle', $titles[$routeName] ?? 'Panel2');
        });    
    }
}
