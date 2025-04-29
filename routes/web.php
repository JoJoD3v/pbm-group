<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\UserRegistrationController;
Route::get('/register', [UserRegistrationController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [UserRegistrationController::class, 'register'])->name('register');

use App\Http\Controllers\LoginController;
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', function(){
    return view('dashboard');
})->middleware('auth')->name('dashboard');


use App\Http\Controllers\MaterialController;
Route::resource('materials', MaterialController::class)->middleware('auth');

use App\Http\Controllers\DepositController;
Route::resource('deposits', DepositController::class)->middleware('auth');

use App\Http\Controllers\CustomerController;
Route::resource('customers', CustomerController::class)->middleware('auth');

use App\Http\Controllers\WarehouseController;
Route::resource('warehouses', WarehouseController::class)->middleware('auth');

use App\Http\Controllers\WorkController;
Route::resource('works', WorkController::class)->middleware('auth');
Route::get('/works/create/disposal', [WorkController::class, 'createDisposal'])->middleware('auth')->name('works.create.disposal');
Route::get('/works/deposits-by-material/{materialId}', [WorkController::class, 'getDepositsByMaterial'])->middleware('auth')->name('works.deposits-by-material');

use App\Http\Controllers\WorkerController;
Route::resource('workers', WorkerController::class)->middleware('auth');

// Rotte per l'assegnazione dei lavori
use App\Http\Controllers\WorkAssignmentController;
Route::get('/work-assignments', [WorkAssignmentController::class, 'index'])->middleware('auth')->name('work.assignments.index');
Route::get('/work-assignments/create', [WorkAssignmentController::class, 'create'])->middleware('auth')->name('work.assignments.create');
Route::post('/work-assignments', [WorkAssignmentController::class, 'store'])->middleware('auth')->name('work.assignments.store');
Route::delete('/work-assignments', [WorkAssignmentController::class, 'destroy'])->middleware('auth')->name('work.assignments.destroy');

// Rotte per gli automezzi
use App\Http\Controllers\VehicleController;
Route::resource('vehicles', VehicleController::class)->middleware('auth');

// Rotte per il report delle assegnazioni degli automezzi
use App\Http\Controllers\VehicleAssignmentReportController;
Route::get('/vehicle-assignments/report', [VehicleAssignmentReportController::class, 'index'])->middleware('auth')->name('vehicle.assignments.report');
Route::delete('/vehicle-assignments/report/{id}', [VehicleAssignmentReportController::class, 'destroy'])->middleware('auth')->name('vehicle.assignments.report.destroy');

// Rotte per l'assegnazione degli automezzi
use App\Http\Controllers\VehicleAssignmentController;
Route::get('/vehicle-assignments', [VehicleAssignmentController::class, 'index'])->middleware('auth')->name('vehicle.assignments.index');
Route::get('/vehicle-assignments/create', [VehicleAssignmentController::class, 'create'])->middleware('auth')->name('vehicle.assignments.create');
Route::post('/vehicle-assignments', [VehicleAssignmentController::class, 'store'])->middleware('auth')->name('vehicle.assignments.store');
Route::get('/vehicle-assignments/{vehicle}/{worker}/edit', [VehicleAssignmentController::class, 'edit'])->middleware('auth')->name('vehicle.assignments.edit');
Route::put('/vehicle-assignments/{vehicle}/{worker}', [VehicleAssignmentController::class, 'update'])->middleware('auth')->name('vehicle.assignments.update');
Route::delete('/vehicle-assignments/{vehicle}/{worker}', [VehicleAssignmentController::class, 'destroy'])->middleware('auth')->name('vehicle.assignments.destroy');

use App\Http\Controllers\CreditCardController;
Route::resource('credit-cards', CreditCardController::class);

use App\Http\Controllers\CreditCardAssignmentController;
Route::resource('credit-card-assignments', CreditCardAssignmentController::class);

use App\Http\Controllers\CreditCardRechargeController;
Route::resource('credit-card-recharges', CreditCardRechargeController::class);
