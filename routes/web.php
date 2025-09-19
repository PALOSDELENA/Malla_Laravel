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
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\PuntoMiddleware;
use App\Livewire\AsignacionTurnosKanban;
use Dom\Document;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\PaloteoController;

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
Route::get('/usuarios', [UsuariosController::class, 'index'])->middleware(['auth', 'verified'])->name('usuarios.index');
Route::put('/usuarios/{usuario}', [UsuariosController::class, 'update'])->middleware(['auth', 'verified'])->name('usuarios.update');
Route::post('/usuarios', [UsuariosController::class, 'store'])->middleware(['auth', 'verified'])->name('usuarios.store');
Route::get('/usuarios/crear', [UsuariosController::class, 'create'])->middleware(['auth', 'verified'])->name('usuarios.create');
Route::delete('/usarios/{usuario}', [UsuariosController::class, 'destroy'])->middleware(['auth', 'verified'])->name('usuarios.destroy');

//Turnos
Route::get('/turnos', [TurnosController::class, 'index'])->middleware(['auth', 'verified'])->name('turnos.index');
Route::post('/turnos', [TurnosController::class, 'store'])->middleware(['auth', 'verified'])->name('turnos.store');
Route::put('/turnos/{turno}', [TurnosController::class, 'update'])->middleware(['auth', 'verified'])->name('turnos.update');
Route::delete('/turnos/{turno}', [TurnosController::class, 'destroy'])->middleware(['auth', 'verified'])->name('turnos.destroy');

// Productos
Route::get('/productos', [ProductoController::class, 'index'])->middleware(['auth', 'verified'])->name('productos.index');
Route::get('/productos/crear', [ProductoController::class, 'create'])->middleware(['auth', 'verified'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->middleware(['auth', 'verified'])->name('productos.store');
Route::put('/productos/{producto}', [ProductoController::class, 'update'])->middleware(['auth', 'verified'])->name('productos.update');
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->middleware(['auth', 'verified'])->name('productos.destroy');

Route::get('/productos/existencias', [ProductoController::class, 'stockChart'])->middleware(['auth', 'verified'])->name('productos.stockChart');

// Cargos
Route::get('/cargos', [CargosController::class, 'index'])->middleware(['auth', 'verified'])->name('cargos.index');
Route::post('/cargos', [CargosController::class, 'store'])->middleware(['auth', 'verified'])->name('cargos.store');
Route::put('/cargos/{cargo}', [CargosController::class, 'update'])->middleware(['auth', 'verified'])->name('cargos.update');
Route::delete('/cargos/{cargo}', [CargosController::class, 'destroy'])->middleware(['auth', 'verified'])->name('cargos.destroy');

// Puntos
Route::get('/puntos', [PuntosController::class, 'index'])->middleware(['auth', 'verified'])->name('puntos.index');
Route::post('/puntos', [PuntosController::class, 'store'])->middleware(['auth', 'verified'])->name('puntos.store');
Route::put('/puntos/{punto}', [PuntosController::class, 'update'])->middleware(['auth', 'verified'])->name('puntos.update');
Route::delete('/puntos/{punto}', [PuntosController::class, 'destroy'])->middleware(['auth', 'verified'])->name('puntos.destroy');

// Tipos de Documentos
Route::get('/tipos-documentos', [TipoDocumentoController::class, 'index'])->middleware(['auth', 'verified'])->name('tipos-documentos.index');
Route::post('/tipos-documentos', [TipoDocumentoController::class, 'store'])->middleware(['auth', 'verified'])->name('tipos-documentos.store');
Route::put('/tipos-documentos/{tipoDocumento}', [TipoDocumentoController::class, 'update'])->middleware(['auth', 'verified'])->name('tipos-documentos.update');
Route::delete('/tipos-documentos/{tipoDocumento}', [TipoDocumentoController::class, 'destroy'])->middleware(['auth', 'verified'])->name('tipos-documentos.destroy');

// Producciones
Route::get('/producciones', [ProduccionController::class, 'index'])->middleware(['auth', 'verified'])->name('producciones.index');
Route::get('/producciones/crear', [ProduccionController::class, 'create'])->middleware(['auth', 'verified'])->name('producciones.create');
Route::post('/producciones', [ProduccionController::class, 'store'])->middleware(['auth', 'verified'])->name('producciones.store');
Route::put('/producciones/{produccion}', [ProduccionController::class, 'update'])->middleware(['auth', 'verified'])->name('producciones.update');
Route::delete('/producciones/{produccion}', [ProduccionController::class, 'destroy'])->middleware(['auth', 'verified'])->name('producciones.destroy');

// Trazabildiad
Route::get('/trazabilidad', [TrazabilidadController::class, 'index'])->name('trazabilidad.index');
Route::get('/trazabilidad/crear', [TrazabilidadController::class, 'create'])->name('trazabilidad.create');
Route::post('/trazabilidad', [TrazabilidadController::class, 'store'])->name('trazabilidad.store');
Route::put('/trazabilidad/{trazabilidad}', [TrazabilidadController::class, 'update'])->name('trazabilidad.update');
Route::delete('/trazabilidad/{trazabilidad}', [TrazabilidadController::class, 'destroy'])->name('trazabilidad.destroy');

// Oreden de Producción
Route::get('/orden-produccion', [OrdenProduccionController::class, 'index'])->name('ordenProduccion.index');
Route::get('/orden-produccion/crear', [OrdenProduccionController::class, 'create'])->name('ordenProduccion.create');
// Route::middleware(['web'])->group(function () {
//     Route::post('/orden-produccion', [OrdenProduccionController::class, 'store'])->name('ordenProduccion.store');
// });
Route::post('/orden-produccion', [OrdenProduccionController::class, 'store'])->name('ordenProduccion.store');
Route::put('/orden-produccion/{ordenProduccion}', [OrdenProduccionController::class, 'update'])->name('ordenProduccion.update');
Route::delete('/orden-produccion/{ordenProduccion}', [OrdenProduccionController::class, 'destroy'])->name('ordenProduccion.destroy');

Route::get('/produccion/{id}/materias-primas', [OrdenProduccionController::class, 'getMateriasPrimas']);
Route::get('/ordenProduccion/{id}/materias-primas-consumo', [OrdenProduccionController::class, 'getConsumosMateriasPrimas']);


// Route::get('/kanban-turnos', AsignacionTurnosKanban::class)->name('kanban.turnos');
// Route::get('/kanban-form', AsignacionTurnosForm::class)->name('kanban.form');

Route::get('/kanban-turnos', function(){
    return view('admin_turnosKanban.kanban');
})->name('kanban.turnos');

// Rutas para el Dashboard
Route::get('/dashboard/admin', [DashboardController::class, 'indexAdmin'])
    ->middleware(PuntoMiddleware::class . ':Administrativo')
    ->name('dashboardAdmin');
Route::get('/dashboard/puente', [DashboardController::class, 'indexPuente'])
    ->middleware(PuntoMiddleware::class . ':Puente Aranda')
    ->name('dashboardPuente');
Route::get('/dashboard/cafam', [DashboardController::class, 'indexCafam'])
    ->middleware(PuntoMiddleware::class . ':Cafam')
    ->name('dashboardCafam');
Route::get('/dashboard/centro', [DashboardController::class, 'indexCentro'])
    ->middleware(PuntoMiddleware::class . ':Centro')
    ->name('dashboardCentro');
Route::get('/dashboard/cocina', [DashboardController::class, 'indexCocina'])
    ->middleware(PuntoMiddleware::class . ':Cocina')
    ->name('dashboardCocina');
Route::get('/dashboard/fontibon', [DashboardController::class, 'indexFon'])
    ->middleware(PuntoMiddleware::class . ':Fontibón')
    ->name('dashboardFon');
Route::get('/dashboard/jimenez', [DashboardController::class, 'indexJim'])
    ->middleware(PuntoMiddleware::class . ':Jiménez')
    ->name('dashboardJim');
Route::get('/dashboard/mall-plaza', [DashboardController::class, 'indexMall'])
    ->middleware(PuntoMiddleware::class . ':Mall Plaza')
    ->name('dashboardMall');
Route::get('/dashboard/multi-plaza', [DashboardController::class, 'indexMulti'])
    ->middleware(PuntoMiddleware::class . ':Multi Plaza')
    ->name('dashboardMulti');
Route::get('/dashboard/nuestro-bogota', [DashboardController::class, 'indexNuestro'])
    ->middleware(PuntoMiddleware::class . ':Nuestro Bogotá')
    ->name('dashboardNuestro');
Route::get('/dashboard/parrilla', [DashboardController::class, 'indexParrilla'])
    ->middleware(PuntoMiddleware::class . ':Parrilla')
    ->name('dashboardParrilla');
Route::get('/dashboard/quinta-paredes', [DashboardController::class, 'indexQuinta'])
    ->middleware(PuntoMiddleware::class . ':Quinta Paredes')
    ->name('dashboardQuinta');
Route::get('/dashboard/salitre-plaza', [DashboardController::class, 'indexSalitre'])
    ->middleware(PuntoMiddleware::class . ':Salitre Plaza')
    ->name('dashboardSalitre');


// Rutas Paloteo
Route::get('/paloteo', [PaloteoController::class, 'index'])->middleware(['auth', 'verified'])->name('paloteo');
Route::get('/paloteo/puntos', [PaloteoController::class, 'obtenerPuntos']);
// Route::get('/paloteo/gerente/{punto}/{fechaInicio}/{fechaFin}', [PaloteoController::class, 'obtenerGerente']);
Route::get('/paloteo/gerente', [PaloteoController::class, 'obtenerGerente']);
Route::get('/paloteo/reporte/semanal/{punto}/{seccion}', [PaloteoController::class, 'reporteSemanal']);
Route::get('/paloteo/productos', [PaloteoController::class, 'obtenerProductos']);
Route::get('/paloteo/get/productos', [PaloteoController::class, 'getProductos']);
Route::put('/paloteo/{id}/productos', [PaloteoController::class, 'asignarSeccion']);
                                                                                                                            Route::put('/productos/{id}', [PaloteoController::class, 'quitarSeccion']);
Route::post('/guardar-inventario', [PaloteoController::class, 'guardarInventario']);
Route::get('/paloteo/historico/{punto}', [PaloteoController::class, 'obtenerHistorico']);
Route::post('/guardar-historico', [PaloteoController::class, 'guardarHistorico']);
Route::get('/paloteo/cargar-historico/{id}', [PaloteoController::class, 'cargarHistorico'])
    ->middleware('auth');

Route::get('/paloteo/export', [PaloteoController::class, 'exportExcel'])
    ->middleware('auth');


//Rutas Orden de Compra
Route::get('/orden-compra', [OrdenCompraController::class, 'index'])->name('ordenCompra');
Route::get('/crear-orden-compra', [OrdenCompraController::class, 'create'])->name('crearOrden');
Route::post('/registrar-orden', [OrdenCompraController::class, 'store'])->name('registrarOrden');

Route::get('/ordenes/{id}/pdf', [OrdenCompraController::class, 'verPDF'])->name('ordenes.ver.pdf');
Route::get('/ordenes/{id}', [OrdenCompraController::class, 'show'])->name('ordenes.show');
Route::delete('/eliminar/{id}', [OrdenCompraController::class, 'destroy'])->name('ordenes.destroy');

Route::post('/ordenes/{id}/revision', [OrdenCompraController::class, 'revision'])
    ->name('ordenes.revision');

Route::get('/edit/orden/{id}', [OrdenCompraController::class, 'edit'])->name('editOrden');
Route::post('/editar/{id}', [OrdenCompraController::class, 'update'])->name('ordenes.update');

Route::post('/registrar-orden-ajax', [OrdenCompraController::class, 'storeAjax'])->name('registrarOrdenAjax');
require __DIR__.'/auth.php';
