<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataCustomers;
use App\Http\Controllers\DataProducts;
use App\Http\Controllers\DataResourceGrid;
use App\Http\Controllers\DataServices;
use App\Http\Controllers\DataEmployees;
use App\Http\Controllers\DataPayment;
use App\Http\Controllers\DataSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/data-resource-grid', [DataResourceGrid::class, 'loadDates']);
    Route::get('/workers', [DataResourceGrid::class, 'loadWorkers']);
    Route::get('/workers-w-payment', [DataResourceGrid::class, 'loadWorkersNPayment']);
    Route::get('/event-details', [DataResourceGrid::class, 'loadEventDetails']);
    Route::get('/validate-giftcard', [DataResourceGrid::class, 'validateGiftCard']);

    Route::put('/update-appointment/{type?}/{isDate?}', [DataResourceGrid::class, 'updateAppointment']);
    Route::put('/update-details', [DataResourceGrid::class, 'updateDetails']);
    
    Route::delete('/delete-date', [DataResourceGrid::class, 'deleteDate']);
    
    Route::put('/update-methods', [DataPayment::class, 'updateMethods']);
    
    Route::get('/uid',function(){return ['uid' => uniqid()];});

    Route::get('/data-customer', [DataCustomers::class, 'loadCustomer']);
    Route::get('/data-customers', [DataCustomers::class, 'loadCustomers']);
    Route::get('/validate-reward-points', [DataCustomers::class, 'validateRewardPoints']);
    Route::get('/data-customers-origin', [DataCustomers::class,'loadOrigins']);

    Route::put('/create-card-cust', [DataCustomers::class,'createCardCust']);
    Route::put('/update-or-create-client', [DataCustomers::class,'storeClient']);
    
    Route::get('/data-products', [DataProducts::class, 'loadProducts']);

    Route::put('/update-or-create-product', [DataProducts::class,'storeProduct']);

    Route::get('/data-employees',[DataEmployees::class,'loadEmployees']);

    Route::put('/is-unique-employee-email',[DataEmployees::class,'isUniqueEmployeeEmail']);
    Route::put('/update-or-create-employee',[DataEmployees::class,'updateOrCreateEmployee']);
    
    Route::get('/data-services', [DataServices::class, 'loadServices']);
    Route::get('/data-suppliers', [DataServices::class, 'loadSuppliers']);

    Route::put('/update-or-create-service', [DataServices::class,'storeService']);

    Route::put('/update-details-sale', [DataSales::class, 'updateOrCreateSale']);
});

// Rutas públicas (sin autenticación)
Route::post('login', [AuthController::class, 'login']);
// Route::post('register', [AuthController::class, 'register']);
// Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Sesión cerrada']);
});
