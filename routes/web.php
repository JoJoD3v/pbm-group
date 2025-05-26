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
    $todayWorks = collect([]);
    
    // Se l'utente è amministratore o sviluppatore, carica i lavori di oggi
    if(in_array(auth()->user()->role, ['Amministratore', 'Sviluppatore'])) {
        $todayWorks = \App\Models\Work::whereDate('data_esecuzione', today())
            ->with('customer')
            ->get();
    }
    
    return view('dashboard', compact('todayWorks'));
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

// Rotte per i report del fondo cassa (amministratore)
use App\Http\Controllers\CashMovementReportController;
use App\Http\Controllers\WorkerCashRechargeController;
Route::middleware(['auth'])->group(function () {
    // Report fondo cassa
    Route::get('/reports/cashflow', [CashMovementReportController::class, 'index'])
        ->name('reports.cashflow.index');
    Route::post('/reports/cashflow/generate', [CashMovementReportController::class, 'generate'])
        ->name('reports.cashflow.generate');
        
    // Ricarica fondo cassa
    Route::get('/worker/cash/recharge', [WorkerCashRechargeController::class, 'index'])
        ->name('worker.cash.recharge');
    Route::post('/worker/cash/recharge', [WorkerCashRechargeController::class, 'store'])
        ->name('worker.cash.recharge.store');
});

// Rotte per i lavoratori (dipendenti)
use App\Http\Controllers\WorkerJobController;
use App\Http\Controllers\WorkerCardController;
use App\Http\Controllers\WorkerCashFlowController;
use App\Http\Controllers\RicevutaController;
use App\Http\Middleware\CheckWorkerRole;

Route::middleware(['auth', CheckWorkerRole::class])->group(function () {
    // Gestione lavori
    Route::get('/worker/jobs', [WorkerJobController::class, 'index'])->name('worker.jobs');
    Route::get('/worker/jobs/{id}', [WorkerJobController::class, 'show'])->name('worker.jobs.show');
    Route::post('/worker/jobs/{id}/assumi', [WorkerJobController::class, 'assumiLavoro'])->name('worker.jobs.assumi');
    
    // Gestione ricevute
    Route::get('/worker/ricevute/create/{workId}', [RicevutaController::class, 'create'])->name('worker.ricevute.create');
    Route::post('/worker/ricevute', [RicevutaController::class, 'store'])->name('worker.ricevute.store');
    
    // Gestione carte
    Route::get('/worker/cards', [WorkerCardController::class, 'index'])->name('worker.cards');
    Route::get('/worker/cards/{id}', [WorkerCardController::class, 'show'])->name('worker.cards.show');
    
    // Gestione fondo cassa
    Route::get('/worker/cashflow', [WorkerCashFlowController::class, 'index'])->name('worker.cashflow');
    
    // Spese
    Route::get('/worker/cashflow/spesa', [WorkerCashFlowController::class, 'createSpesa'])->name('worker.cashflow.spesa.create');
    Route::post('/worker/cashflow/spesa', [WorkerCashFlowController::class, 'storeSpesa'])->name('worker.cashflow.spesa.store');
    
    // Incassi
    Route::get('/worker/cashflow/incasso', [WorkerCashFlowController::class, 'createIncasso'])->name('worker.cashflow.incasso.create');
    Route::post('/worker/cashflow/incasso', [WorkerCashFlowController::class, 'storeIncasso'])->name('worker.cashflow.incasso.store');
});
