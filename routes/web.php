<?php

use App\Http\Controllers\CargosController;
use App\Http\Controllers\OrdenProduccionController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\ClienteController;
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
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\NovedadPaloteoController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\PaloteoController;
use App\Http\Controllers\ProveedorController;

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
Route::get('/usuarios', [UsuariosController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('usuarios.index');
Route::put('/usuarios/{usuario}', [UsuariosController::class, 'update'])
    ->middleware(['auth', 'verified'])->name('usuarios.update');
Route::post('/usuarios', [UsuariosController::class, 'store'])
    ->middleware(['auth', 'verified'])->name('usuarios.store');
Route::get('/usuarios/crear', [UsuariosController::class, 'create'])
    ->middleware(['auth', 'verified'])->name('usuarios.create');
Route::delete('/usarios/{usuario}', [UsuariosController::class, 'destroy'])
    ->middleware(['auth', 'verified'])->name('usuarios.destroy');

//Turnos
Route::get('/turnos', [TurnosController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('turnos.index');
Route::post('/turnos', [TurnosController::class, 'store'])
    ->middleware(['auth', 'verified'])->name('turnos.store');
Route::put('/turnos/{turno}', [TurnosController::class, 'update'])
    ->middleware(['auth', 'verified'])->name('turnos.update');
Route::delete('/turnos/{turno}', [TurnosController::class, 'destroy'])
    ->middleware(['auth', 'verified'])->name('turnos.destroy');

// Productos
Route::get('/productos', [ProductoController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('productos.index');
Route::get('/productos/crear', [ProductoController::class, 'create'])
    ->middleware(['auth', 'verified'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])
    ->middleware(['auth', 'verified'])->name('productos.store');
Route::put('/productos/{producto}', [ProductoController::class, 'update'])
    ->middleware(['auth', 'verified'])->name('productos.update');
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
    ->middleware(['auth', 'verified'])->name('productos.destroy');

Route::get('/productos/existencias', [ProductoController::class, 'stockChart'])
    ->middleware(['auth', 'verified'])->name('productos.stockChart');

// Cargos
Route::get('/cargos', [CargosController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('cargos.index');
Route::post('/cargos', [CargosController::class, 'store'])
    ->middleware(['auth', 'verified'])->name('cargos.store');
Route::put('/cargos/{cargo}', [CargosController::class, 'update'])
    ->middleware(['auth', 'verified'])->name('cargos.update');
Route::delete('/cargos/{cargo}', [CargosController::class, 'destroy'])
    ->middleware(['auth', 'verified'])->name('cargos.destroy');

// Puntos
Route::get('/puntos', [PuntosController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('puntos.index');
Route::post('/puntos', [PuntosController::class, 'store'])
    ->middleware(['auth', 'verified'])->name('puntos.store');
Route::put('/puntos/{punto}', [PuntosController::class, 'update'])
    ->middleware(['auth', 'verified'])->name('puntos.update');
Route::delete('/puntos/{punto}', [PuntosController::class, 'destroy'])
    ->middleware(['auth', 'verified'])->name('puntos.destroy');

// Tipos de Documentos
Route::get('/tipos-documentos', [TipoDocumentoController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('tipos-documentos.index');
Route::post('/tipos-documentos', [TipoDocumentoController::class, 'store'])
    ->middleware(['auth', 'verified'])->name('tipos-documentos.store');
Route::put('/tipos-documentos/{tipoDocumento}', [TipoDocumentoController::class, 'update'])
    ->middleware(['auth', 'verified'])->name('tipos-documentos.update');
Route::delete('/tipos-documentos/{tipoDocumento}', [TipoDocumentoController::class, 'destroy'])
    ->middleware(['auth', 'verified'])->name('tipos-documentos.destroy');

// Producciones
Route::get('/producciones', [ProduccionController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('producciones.index');
Route::get('/producciones/crear', [ProduccionController::class, 'create'])
    ->middleware(['auth', 'verified'])->name('producciones.create');
Route::post('/producciones', [ProduccionController::class, 'store'])
    ->middleware(['auth', 'verified'])->name('producciones.store');
Route::put('/producciones/{produccion}', [ProduccionController::class, 'update'])
    ->middleware(['auth', 'verified'])->name('producciones.update');
Route::delete('/producciones/{produccion}', [ProduccionController::class, 'destroy'])
    ->middleware(['auth', 'verified'])->name('producciones.destroy');

// Trazabildiad
Route::get('/trazabilidad', [TrazabilidadController::class, 'index'])->name('trazabilidad.index')
    ->middleware('auth');
Route::get('/trazabilidad/crear', [TrazabilidadController::class, 'create'])->name('trazabilidad.create')
    ->middleware('auth');
Route::post('/trazabilidad', [TrazabilidadController::class, 'store'])->name('trazabilidad.store')
    ->middleware('auth');
Route::put('/trazabilidad/{trazabilidad}', [TrazabilidadController::class, 'update'])->name('trazabilidad.update')
    ->middleware('auth');
Route::delete('/trazabilidad/{trazabilidad}', [TrazabilidadController::class, 'destroy'])->name('trazabilidad.destroy')
    ->middleware('auth');

// Oreden de Producción
Route::get('/orden-produccion', [OrdenProduccionController::class, 'index'])->name('ordenProduccion.index')
    ->middleware('auth');
Route::get('/orden-produccion/crear', [OrdenProduccionController::class, 'create'])->name('ordenProduccion.create')
    ->middleware('auth');
Route::post('/orden-produccion', [OrdenProduccionController::class, 'store'])->name('ordenProduccion.store')
    ->middleware('auth');
Route::put('/orden-produccion/{ordenProduccion}', [OrdenProduccionController::class, 'update'])->name('ordenProduccion.update')
    ->middleware('auth');
Route::delete('/orden-produccion/{ordenProduccion}', [OrdenProduccionController::class, 'destroy'])->name('ordenProduccion.destroy')
    ->middleware('auth');

Route::get('/produccion/{id}/materias-primas', [OrdenProduccionController::class, 'getMateriasPrimas'])
    ->middleware('auth');
Route::get('/ordenProduccion/{id}/materias-primas-consumo', [OrdenProduccionController::class, 'getConsumosMateriasPrimas'])
    ->middleware('auth');


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
Route::get('/dashboard/hayuelos', [DashboardController::class, 'indexHayuelos'])
    ->middleware(PuntoMiddleware::class . ':Hayuelos')
    ->name('dashboardHayuelos');

// Rutas Paloteo
Route::get('/paloteo', [PaloteoController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('paloteo');
Route::get('/paloteo/puntos', [PaloteoController::class, 'obtenerPuntos']);
// Route::get('/paloteo/gerente/{punto}/{fechaInicio}/{fechaFin}', [PaloteoController::class, 'obtenerGerente']);
Route::get('/paloteo/gerente', [PaloteoController::class, 'obtenerGerente'])
    ->middleware('auth');
Route::get('/paloteo/reporte/semanal/{punto}/{seccion}', [PaloteoController::class, 'reporteSemanal'])
    ->middleware('auth');
Route::get('/paloteo/productos', [PaloteoController::class, 'obtenerProductos'])
    ->middleware('auth');
Route::get('/paloteo/get/productos', [PaloteoController::class, 'getProductos'])
    ->middleware('auth');
Route::put('/paloteo/{id}/productos', [PaloteoController::class, 'asignarSeccion'])
    ->middleware('auth');                                                                                                                         
Route::put('/productos/{id}', [PaloteoController::class, 'quitarSeccion'])
    ->middleware('auth');
Route::post('/guardar-inventario', [PaloteoController::class, 'guardarInventario'])
    ->middleware('auth');
Route::get('/paloteo/historico/{punto}', [PaloteoController::class, 'obtenerHistorico'])
    ->middleware('auth');
Route::post('/guardar-historico', [PaloteoController::class, 'guardarHistorico'])
    ->middleware('auth');
Route::get('/paloteo/cargar-historico/{id}', [PaloteoController::class, 'cargarHistorico'])
    ->middleware('auth');
Route::get('/paloteo/export', [PaloteoController::class, 'exportExcel'])
    ->middleware('auth');


//Rutas Orden de Compra
Route::get('/orden-compra', [OrdenCompraController::class, 'index'])->name('ordenCompra')
    ->middleware('auth');
Route::get('/crear-orden-compra', [OrdenCompraController::class, 'create'])->name('crearOrden')
    ->middleware('auth');
Route::post('/registrar-orden', [OrdenCompraController::class, 'store'])->name('registrarOrden')
    ->middleware('auth');
Route::get('/ordenes/{id}/pdf', [OrdenCompraController::class, 'verPDF'])->name('ordenes.ver.pdf')
    ->middleware('auth');
Route::get('/ordenes/{id}', [OrdenCompraController::class, 'show'])->name('ordenes.show')
    ->middleware('auth');
Route::delete('/eliminar/{id}', [OrdenCompraController::class, 'destroy'])->name('ordenes.destroy')
    ->middleware('auth');
Route::post('/ordenes/{id}/revision', [OrdenCompraController::class, 'revision'])->name('ordenes.revision')
    ->middleware('auth');
Route::get('/edit/orden/{id}', [OrdenCompraController::class, 'edit'])->name('editOrden')
    ->middleware('auth');
Route::post('/editar/{id}', [OrdenCompraController::class, 'update'])->name('ordenes.update')
    ->middleware('auth');
Route::post('/registrar-orden-ajax', [OrdenCompraController::class, 'storeAjax'])->name('registrarOrdenAjax')
    ->middleware('auth');

//Informes
Route::get('/informes', [InformeController::class, 'index'])->name('informe.index')
    ->middleware('auth');
Route::get('/informes/test', [InformeController::class, 'obtenerConsumo'])
    ->middleware('auth');
Route::get('/informes/exportarCompraInsumos', [InformeController::class,  'exportarCompraInsumos'])->name('exportar.insumos')
    ->middleware('auth');
Route::get('/informes/exportarConsumoInsumos', [InformeController::class,  'exportarConsumoInsumos'])->name('exportar.consumo')
    ->middleware('auth');
Route::get('/informes/exportarFacturasPro', [InformeController::class,  'exportarFacturasPro'])->name('exportar.facturas.proveedor')
    ->middleware('auth');
Route::get('/informes/exportarFacturasSer', [InformeController::class,  'exportarFacturasSer'])->name('exportar.facturas.servicios')
    ->middleware('auth');
Route::get('/informes/exportarRecetas', [InformeController::class,  'exportarRecetas'])->name('exportar.recetas')
    ->middleware('auth');
Route::get('/informes/exportarEncuesta', [InformeController::class,  'exportarEncuesta'])->name('exportar.encuesta')
    ->middleware('auth');

//Novedades Paloteo
Route::get('/novedades', [NovedadPaloteoController::class, 'index'])->name('novedad.index')
    ->middleware('auth');
Route::post('/novedades/create', [NovedadPaloteoController::class, 'store'])->name('novedad.store')
    ->middleware('auth');
Route::put('/novedades/update/{id}', [NovedadPaloteoController::class, 'update'])->name('novedad.update')
    ->middleware('auth');
Route::get('/novedades/exportar', [NovedadPaloteoController::class, 'exportarExcel'])->name('novedad.exportar')
    ->middleware('auth');

//Novedad Proveedor
Route::get('/novedad/proveedor', [ProveedorController::class, 'index'])->name('nov.proveedor.index');
Route::get('/novedad/productos/{id}', [ProveedorController::class, 'getProducts']);
Route::post('/novedad/proveedor/create', [ProveedorController::class, 'store'])->name('nov.proveedor.store');

Route::get('/filtrar-novedades', [ProveedorController::class, 'filtrar'])->name('novedades.filtrar');
Route::get('/novedades/exportar-excel', [ProveedorController::class, 'exportarExcel'])->name('novedades.exportarExcel');
Route::get('/filtrar-novedades-fetch', [ProveedorController::class, 'filtrarFetch'])->name('novedades.filtrarFetch');

//Cotizaciones
Route::get('/cotizaciones', [CotizacionController::class, 'index'])->name('coti.index');
Route::get('/cotizaciones/crear', [CotizacionController::class, 'create'])->name('coti.create');
Route::post('/cotizaciones/crear', [CotizacionController::class, 'store'])->name('coti.store');
Route::get('/cotizaciones/ver/{id}', [CotizacionController::class, 'show'])->name('coti.show');
Route::get('/cotizaciones/{id}/export', [CotizacionController::class, 'exportExcel'])->name('coti.export');
Route::get('/cotizaciones/{id}/export-pdf', [CotizacionController::class, 'exportPdf'])->name('coti.export.pdf');
Route::get('/cotizaciones/editar/{id}', [CotizacionController::class, 'edit'])->name('coti.edit');
Route::put('/cotizaciones/editar/{id}', [CotizacionController::class, 'update'])->name('coti.update');
Route::delete('/cotizaciones/{id}', [CotizacionController::class, 'destroy'])->name('coti.destroy');
Route::post('/cotizaciones/{id}/upload-factura', [CotizacionController::class, 'uploadFactura'])->name('coti.uploadFactura');

// Clientes (creación via modal AJAX)
Route::post('/clientes', [ClienteController::class, 'store'])
    ->middleware('auth')->name('clientes.store');

require __DIR__.'/auth.php';
