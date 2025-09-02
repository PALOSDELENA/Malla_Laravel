<?php

use App\Http\Controllers\Api\PaloteoApiController;
use App\Http\Controllers\Api\PuntosApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/puntos', [PuntosApiController::class,'index'])->name('puntos');
Route::post('/puntos', [PuntosApiController::class,'store'])->name('nuevo.punto');

Route::put('/paloteo/{id}/productos', [PaloteoApiController::class, 'asignarSeccion']);
Route::put('/productos/{id}', [PaloteoApiController::class, 'quitarSeccion']);

