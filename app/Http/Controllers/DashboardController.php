<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function indexAdmin() 
    {
        return view('dashboard.palos.dash');
    }
    public function indexPuente() 
    {
        return view('dashboard.puente');
    }
    public function indexCafam() 
    {
        return view('dashboard.cafam');
    }
    public function indexCentro() 
    {
        return view('dashboard.centro');
    }
    public function indexCocina() 
    {
        return view('dashboard.cocina');
    }
    public function indexFon() 
    {
        return view('dashboard.fontibon');
    }
    public function indexJim() 
    {
        return view('dashboard.jimenez');
    }
    public function indexMall() 
    {
        return view('dashboard.mallplaza');
    }
    public function indexMulti() 
    {
        return view('dashboard.multiplaza');
    }
    public function indexNuestro() 
    {
        return view('dashboard.nuestrobogota');
    }
    public function indexParrilla() 
    {
        return view('dashboard.parrilla');
    }
    public function indexQuinta() 
    {
        return view('dashboard.quinta');
    }
    public function indexSalitre() 
    {
        return view('dashboard.salitre');
    }
    public function indexHayuelos() 
    {
        return view('dashboard.hayuelos');
    }
}
