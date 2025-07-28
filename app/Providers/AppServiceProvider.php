<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

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
        View::composer('*', function ($view) {
            $routeName = Route::currentRouteName();
            $titles = [
                'dashboard' => 'Home',
                'users.index' => 'Usuarios',
                'reports.index' => 'Reportes',
                'profile.edit' => 'Profile',
                // Agrega más según tus rutas
                'productos.index' => 'Productos',
            ];

            $view->with('pageTitle', $titles[$routeName] ?? 'Panel2');
        });    
    }
}
