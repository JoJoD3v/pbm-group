<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login.form');
});


use App\Http\Controllers\UserRegistrationController;
Route::get('/register', [UserRegistrationController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [UserRegistrationController::class, 'register'])->name('register');

use App\Http\Controllers\LoginController;
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', function(\Illuminate\Http\Request $request){
    $dateParam = $request->query('date');
    try {
        $currentDate = $dateParam ? \Carbon\Carbon::parse($dateParam)->startOfDay() : \Carbon\Carbon::today();
    } catch (\Exception $e) {
        $currentDate = \Carbon\Carbon::today();
    }

    $todayWorks = collect([]);
    $workerTodayWorks = collect([]);
    $tomorrowFirstWork = null;

    $user = auth()->user();
    $role = strtolower($user->role ?? '');

    // Se l'utente e' amministratore o sviluppatore, carica i lavori del giorno selezionato
    if (in_array($role, ['amministratore', 'sviluppatore'])) {
        $todayWorks = \App\Models\Work::whereDate('data_esecuzione', $currentDate)
            ->with('customer')
            ->get();
    }

    // Se l'utente e' dipendente, carica i lavori assegnati del giorno selezionato e il primo lavoro del giorno successivo
    if ($role === 'dipendente') {
        $worker = $user->worker;

        if ($worker) {
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();
            $nextDayStart = $currentDate->copy()->addDay()->startOfDay();
            $nextDayEnd = $currentDate->copy()->addDay()->endOfDay();

            $workerTodayWorks = $worker->works()
                ->with('customer')
                ->whereBetween('data_esecuzione', [$dayStart, $dayEnd])
                ->orderBy('data_esecuzione')
                ->get();

            $tomorrowFirstWork = $worker->works()
                ->with('customer')
                ->whereBetween('data_esecuzione', [$nextDayStart, $nextDayEnd])
                ->orderBy('data_esecuzione')
                ->first();
        }
    }

    return view('dashboard', compact('todayWorks', 'workerTodayWorks', 'tomorrowFirstWork', 'currentDate'));
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
Route::get('/works/assigned', [WorkController::class, 'assigned'])->middleware('auth')->name('works.assigned');
Route::get('/works/unassigned', [WorkController::class, 'unassigned'])->middleware('auth')->name('works.unassigned');
Route::post('/works/statuses', [WorkController::class, 'statuses'])->middleware('auth')->name('works.statuses');
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
use App\Http\Controllers\AdminFondoCassaController;
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

    // Gestione fondo cassa (lista + modifica) - admin
    Route::get('/admin/fondo-cassa', [AdminFondoCassaController::class, 'index'])
        ->name('admin.fondo-cassa.index');
    Route::get('/admin/fondo-cassa/{worker}/edit', [AdminFondoCassaController::class, 'edit'])
        ->name('admin.fondo-cassa.edit');
    Route::put('/admin/fondo-cassa/{worker}', [AdminFondoCassaController::class, 'update'])
        ->name('admin.fondo-cassa.update');
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
    Route::post('/worker/jobs/{id}/status', [WorkerJobController::class, 'updateStatus'])->name('worker.jobs.status');

    // Gestione ricevute
    Route::get('/worker/ricevute/create/{workId}', [RicevutaController::class, 'create'])->name('worker.ricevute.create');
    Route::post('/worker/ricevute', [RicevutaController::class, 'store'])->name('worker.ricevute.store');
    Route::get('/worker/ricevute/{ricevutaId}/pdf', [RicevutaController::class, 'downloadPDF'])->name('worker.ricevute.pdf');

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

// Visualizzazione bolla per admin/sviluppatore o dipendente assegnato
Route::get('/ricevute/bolle/{ricevutaId}', [RicevutaController::class, 'viewBolla'])
    ->middleware('auth')
    ->name('ricevute.bolle.view');

// PDF ricevuta per admin/sviluppatore o dipendente assegnato
Route::get('/ricevute/{ricevutaId}/pdf', [RicevutaController::class, 'downloadPDF'])
    ->middleware('auth')
    ->name('ricevute.pdf');

// Rotte per la gestione utenti (solo sviluppatori)
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckDeveloperRole;

Route::middleware(['auth', CheckDeveloperRole::class])->group(function () {
    Route::resource('users', UserController::class);
});

