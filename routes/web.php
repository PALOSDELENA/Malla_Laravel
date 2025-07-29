<?php

use App\Http\Controllers\CargosController;
use App\Http\Controllers\OrdenProduccionController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PuntosController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\TrazabilidadController;
use App\Http\Controllers\TurnosController;
use App\Http\Controllers\UsuariosController;
use App\Models\User;
use Dom\Document;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('loginInitial');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Usuarios
Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
Route::put('/usuarios/{usuario}', [UsuariosController::class, 'update'])->name('usuarios.update');
Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
Route::get('/usuarios/crear', [UsuariosController::class, 'create'])->name('usuarios.create');
Route::delete('/usarios/{usuario}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');

//Turnos
Route::get('/turnos', [TurnosController::class, 'index'])->name('turnos.index');
Route::post('/turnos', [TurnosController::class, 'store'])->name('turnos.store');
Route::put('/turnos/{turno}', [TurnosController::class, 'update'])->name('turnos.update');
Route::delete('/turnos/{turno}', [TurnosController::class, 'destroy'])->name('turnos.destroy');

// Productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

// Cargos
Route::get('/cargos', [CargosController::class, 'index'])->name('cargos.index');
Route::post('/cargos', [CargosController::class, 'store'])->name('cargos.store');
Route::put('/cargos/{cargo}', [CargosController::class, 'update'])->name('cargos.update');
Route::delete('/cargos/{cargo}', [CargosController::class, 'destroy'])->name('cargos.destroy');

// Puntos
Route::get('/puntos', [PuntosController::class, 'index'])->name('puntos.index');
Route::post('/puntos', [PuntosController::class, 'store'])->name('puntos.store');
Route::put('/puntos/{punto}', [PuntosController::class, 'update'])->name('puntos.update');
Route::delete('/puntos/{punto}', [PuntosController::class, 'destroy'])->name('puntos.destroy');

// Tipos de Documentos
Route::get('/tipos-documentos', [TipoDocumentoController::class, 'index'])->name('tipos-documentos.index');
Route::post('/tipos-documentos', [TipoDocumentoController::class, 'store'])->name('tipos-documentos.store');
Route::put('/tipos-documentos/{tipoDocumento}', [TipoDocumentoController::class, 'update'])->name('tipos-documentos.update');
Route::delete('/tipos-documentos/{tipoDocumento}', [TipoDocumentoController::class, 'destroy'])->name('tipos-documentos.destroy');

// Producciones
Route::get('/producciones', [ProduccionController::class, 'index'])->name('producciones.index');
Route::get('/producciones/crear', [ProduccionController::class, 'create'])->name('producciones.create');
Route::post('/producciones', [ProduccionController::class, 'store'])->name('producciones.store');
Route::put('/producciones/{produccion}', [ProduccionController::class, 'update'])->name('producciones.update');
Route::delete('/producciones/{produccion}', [ProduccionController::class, 'destroy'])->name('producciones.destroy');

// Trazabildiad
Route::get('/trazabilidad', [TrazabilidadController::class, 'index'])->name('trazabilidad.index');
Route::get('/trazabilidad/crear', [TrazabilidadController::class, 'create'])->name('trazabilidad.create');
Route::post('/trazabilidad', [TrazabilidadController::class, 'store'])->name('trazabilidad.store');
Route::put('/trazabilidad/{trazabilidad}', [TrazabilidadController::class, 'update'])->name('trazabilidad.update');
Route::delete('/trazabilidad/{trazabilidad}', [TrazabilidadController::class, 'destroy'])->name('trazabilidad.destroy');

// Oreden de Producción
Route::get('/orden-produccion', [OrdenProduccionController::class, 'index'])->name('ordenProduccion.index');
Route::get('/orden-produccion/crear', [OrdenProduccionController::class, 'create'])->name('ordenProduccion.create');
Route::post('/orden-produccion', [OrdenProduccionController::class, 'store'])->name('ordenProduccion.store');
Route::put('/orden-produccion/{ordenProduccion}', [OrdenProduccionController::class, 'update'])->name('ordenProduccion.update');
Route::delete('/orden-produccion/{ordenProduccion}', [OrdenProduccionController::class, 'destroy'])->name('ordenProduccion.destroy');

Route::get('/produccion/{id}/materias-primas', [OrdenProduccionController::class, 'getMateriasPrimas']);


require __DIR__.'/auth.php';
