<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaController;
use App\Http\Livewire\Agenda;
use App\Http\Livewire\Ajustes;
use App\Http\Livewire\CategoriaClientes;
use App\Http\Livewire\CategoriaProductos;
use App\Http\Livewire\CategoriaServicios;
use App\Http\Livewire\Clientes;
use App\Http\Livewire\Configuration;
use App\Http\Livewire\Corte;
use App\Http\Livewire\Empleados;
use App\Http\Livewire\EncuestaClientes;
use App\Http\Livewire\Gastos;
use App\Http\Livewire\HistoricoCliente;
use App\Http\Livewire\Informe;
use App\Http\Livewire\InformeEmpleados;
use App\Http\Livewire\InformeMovimientos;
use App\Http\Livewire\Marcas;
use App\Http\Livewire\MisGanancias;
use App\Http\Livewire\Productos;
use App\Http\Livewire\Reporte;
use App\Http\Livewire\Servicios;
use App\Http\Livewire\Suscripcion;
use App\Http\Livewire\Ventas;
use App\Http\Livewire\Comisiones;
use App\Http\Livewire\Propinas;
use Illuminate\Support\Facades\Route;






/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::view('/aviso-privacidad', 'aviso-privacidad');
Route::view('/seguridad', 'seguridad');
Route::view('/condiciones-uso', 'condiciones-uso');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(["middleware" => "role:admin"],function () {
    Route::get('ajustes', Ajustes::class)->name('ajustes');
    Route::get('configuracion', Configuration::class)->name('configuracion');
    Route::get('actividad-empleados', InformeEmpleados::class)->name('actividad-empleados'); 
});
Route::group(["middleware" => "role:recepcionista,admin"],function () {
    Route::get('gastos', Gastos::class)->name('gastos');
    Route::get('informe', Informe::class)->name('informe'); 
    Route::get('informe-movimientos', InformeMovimientos::class)->name('informe-movimientos'); 
    Route::get('informe-historico', Informe::class)->name('informe-historico'); 
    Route::get('informe-caja', Corte::class)->name('informe-caja'); 
    Route::get('comisiones-empleados/{selectedEmployeeId?}', Comisiones::class)->name('comisiones-empleados'); 
    Route::get('propinas-empleados', Propinas::class)->name('propinas-empleados'); 
    Route::get('categoria-clientes', CategoriaClientes::class)->name('categoria-clientes');
    Route::get('reporte/{tipo?}/{data?}', Reporte::class)->name('reporte');
    Route::get('categoria-servicios', CategoriaServicios::class)->name('categoria-servicios');
    Route::get('categoria-productos', CategoriaProductos::class)->name('categoria-productos');
    Route::get('empleados/{search?}', Empleados::class)->name('empleados');
    Route::get('marcas/{search?}', Marcas::class)->name('marcas');
    Route::get('ventas/{venta_id?}', Ventas::class)->name('ventas');
    Route::get('citas/{action?}/{pestaña?}/{cita_id?}', Agenda::class)->name('citas');
    Route::get('productos/{pestaña?}/{search?}', Productos::class)->name('productos');
    Route::get('servicios/{search?}', Servicios::class)->name('servicios');
    Route::get('clientes/{custId?}/{search?}', Clientes::class)->name('clientes');
    Route::get('historico-cliente/{search?}/{pestaña?}', HistoricoCliente::class)->name('historico-cliente');
    Route::get('agenda/{action?}/{pestaña?}', Agenda::class)->name('agenda');
    Route::get('/', Agenda::class)->name('/');
    Route::get('suscripcion', Suscripcion::class)->name('suscripcion');
    Route::get('mis-ganancias', MisGanancias::class)->name('mis-ganancias');
    Route::get('data/categoriesS',[DataController::class, 'getCategoriesS'])->name('data.categoriesS');
    Route::get('data/categories',[DataController::class, 'getCategories'])->name('data.categories');
    Route::get('data/categoriesCustomer',[DataController::class, 'getCategoriesCustomer'])->name('data.categoriesCustomer');
    Route::get('data/brands',[DataController::class, 'getBrands'])->name('data.brands');
    Route::get('data/customers',[DataController::class, 'getCustomers'])->name('data.customers');
    Route::get('data/services',[DataController::class, 'getServices'])->name('data.services');
    Route::get('data/etiquetas',[DataController::class, 'getTags'])->name('data.etiquetas');
    
    Route::get('/envia/{uid}', [WaController::class, 'envia'])->name('envia');
    
});

Route::view('/cita-confirmada', 'cita-confirmada');
Route::view('/encuesta-contestada', 'encuesta-contestada');
Route::get('encuesta', EncuestaClientes::class)->name('encuesta');
Route::get('/aceptacion-cita/{uid}', [WaController::class, 'aceptarCita'])->name('aceptacion-cita');

Route::prefix('mercadopagos')->group(function(){
    Route::post('recibirpago',[Suscripcion::class,'recibirPago']);
});

require __DIR__.'/auth.php';
