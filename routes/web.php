<?php

use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

Route::get('/dashboard', function (Request $request) {
    $dateParam = $request->query('date');
    try {
        $currentDate = $dateParam ? Carbon::parse($dateParam)->startOfDay() : Carbon::today();
    } catch (Exception $e) {
        $currentDate = Carbon::today();
    }

    $todayWorks = collect([]);
    $workerTodayWorks = collect([]);
    $tomorrowFirstWork = null;
    $worker = null;
    $carteAssegnate = collect([]);
    $tipiAccessibili = [];
    $tab = 'tutti';

    $user = auth()->user();
    $role = strtolower($user->role ?? '');

    // Se l'utente e' amministratore o sviluppatore, carica i lavori del giorno selezionato
    if (in_array($role, ['amministratore', 'sviluppatore'])) {
        $todayWorks = Work::whereDate('data_esecuzione', $currentDate)
            ->with(['customer', 'workers'])
            ->orderBy('data_esecuzione')
            ->get();
    }

    // Se l'utente e' dipendente, carica i lavori assegnati del giorno selezionato e il primo lavoro del giorno successivo
    if ($role === 'dipendente') {
        $worker = $user->worker;

        if ($worker) {
            $carteAssegnate = $worker->assignedCreditCards()->get();
            $tipiAccessibili = $worker->tipiLavoroAccessibili();

            $tab = $request->query('tab', 'tutti');
            if ($tab !== 'tutti' && ! in_array($tab, $tipiAccessibili)) {
                $tab = 'tutti';
            }
            $tipiQuery = ($tab === 'tutti') ? $tipiAccessibili : [$tab];

            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();
            $nextDayStart = $currentDate->copy()->addDay()->startOfDay();
            $nextDayEnd = $currentDate->copy()->addDay()->endOfDay();

            $workerTodayWorks = $worker->works()
                ->with('customer')
                ->whereIn('tipo_lavoro', $tipiQuery)
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

    return view('dashboard', compact('todayWorks', 'workerTodayWorks', 'tomorrowFirstWork', 'currentDate', 'worker', 'carteAssegnate', 'tipiAccessibili', 'tab'));
})->middleware('auth')->name('dashboard');

use App\Http\Controllers\MaterialController;

Route::resource('materials', MaterialController::class)->middleware('auth');

use App\Http\Controllers\DepositController;

Route::resource('deposits', DepositController::class)->middleware('auth');

use App\Http\Controllers\ServiceController;

Route::resource('services', ServiceController::class)->middleware('auth');

use App\Http\Controllers\CustomerController;

Route::resource('customers', CustomerController::class)->middleware('auth');

use App\Http\Controllers\AppaltatoreController;

Route::resource('appaltatori', AppaltatoreController::class)->middleware('auth');

use App\Http\Controllers\WarehouseController;

Route::resource('warehouses', WarehouseController::class)->middleware('auth');

use App\Http\Controllers\WorkController;

Route::get('/works/assigned', [WorkController::class, 'assigned'])->middleware('auth')->name('works.assigned');
Route::get('/works/unassigned', [WorkController::class, 'unassigned'])->middleware('auth')->name('works.unassigned');
Route::post('/works/statuses', [WorkController::class, 'statuses'])->middleware('auth')->name('works.statuses');
Route::post('/works/{work}/complete', [WorkController::class, 'complete'])->middleware('auth')->name('works.complete');
Route::resource('works', WorkController::class)->middleware('auth');
Route::get('/works/create/disposal', [WorkController::class, 'createDisposal'])->middleware('auth')->name('works.create.disposal');
Route::get('/works/create/servizi', [WorkController::class, 'createServizi'])->middleware('auth')->name('works.create.servizi');
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
use App\Http\Controllers\AdminFondoCassaController;
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

    // Gestione fondo cassa (lista + modifica) - admin
    Route::get('/admin/fondo-cassa', [AdminFondoCassaController::class, 'index'])
        ->name('admin.fondo-cassa.index');
    Route::get('/admin/fondo-cassa/{worker}/edit', [AdminFondoCassaController::class, 'edit'])
        ->name('admin.fondo-cassa.edit');
    Route::put('/admin/fondo-cassa/{worker}', [AdminFondoCassaController::class, 'update'])
        ->name('admin.fondo-cassa.update');
});

// Rotte per i lavoratori (dipendenti)
use App\Http\Controllers\BorderoController;
use App\Http\Controllers\PezzoBorderoController;
use App\Http\Controllers\RicevutaController;
use App\Http\Controllers\WorkerCardController;
use App\Http\Controllers\WorkerCashFlowController;
use App\Http\Controllers\WorkerJobController;
use App\Http\Middleware\CheckWorkerRole;

Route::middleware(['auth', CheckWorkerRole::class])->group(function () {
    // Gestione lavori
    Route::get('/worker/jobs', [WorkerJobController::class, 'index'])->name('worker.jobs');
    Route::get('/worker/jobs/{id}', [WorkerJobController::class, 'show'])->name('worker.jobs.show');
    Route::post('/worker/jobs/{id}/assumi', [WorkerJobController::class, 'assumiLavoro'])->name('worker.jobs.assumi');
    Route::post('/worker/jobs/{id}/status', [WorkerJobController::class, 'updateStatus'])->name('worker.jobs.status');
    Route::post('/worker/jobs/{id}/spesa', [WorkerJobController::class, 'storeSpesaLavoro'])->name('worker.jobs.spesa.store');
    Route::post('/worker/jobs/{id}/incasso', [WorkerJobController::class, 'storeIncassoLavoro'])->name('worker.jobs.incasso.store');
    Route::post('/worker/jobs/{id}/movimento', [WorkerJobController::class, 'storeMovimentoLavoro'])->name('worker.jobs.movimento.store');

    // Gestione ricevute
    Route::get('/worker/ricevute/create/{workId}', [RicevutaController::class, 'create'])->name('worker.ricevute.create');
    Route::post('/worker/ricevute', [RicevutaController::class, 'store'])->name('worker.ricevute.store');
    Route::get('/worker/ricevute/{ricevutaId}/pdf', [RicevutaController::class, 'downloadPDF'])->name('worker.ricevute.pdf');

    // Gestione borderò
    Route::get('/worker/bordero/{workId}', [BorderoController::class, 'edit'])->name('worker.bordero.edit');
    Route::post('/worker/bordero/{workId}', [BorderoController::class, 'save'])->name('worker.bordero.save');
    Route::get('/worker/bordero/{workId}/pdf', [BorderoController::class, 'downloadPDF'])->name('worker.bordero.pdf');

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

// Invia fattura via email (solo admin/sviluppatore)
Route::post('/ricevute/{ricevutaId}/send-email', [RicevutaController::class, 'sendEmail'])
    ->middleware('auth')
    ->name('ricevute.send-email');

// Gestione ricevute lato admin (solo admin/sviluppatore)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/ricevute/create/{workId}', [RicevutaController::class, 'adminCreate'])->name('admin.ricevute.create');
    Route::post('/admin/ricevute', [RicevutaController::class, 'adminStore'])->name('admin.ricevute.store');
    Route::get('/admin/ricevute/{ricevutaId}/edit', [RicevutaController::class, 'adminEdit'])->name('admin.ricevute.edit');
    Route::put('/admin/ricevute/{ricevutaId}', [RicevutaController::class, 'adminUpdate'])->name('admin.ricevute.update');
});

// PDF borderò per admin/sviluppatore o dipendente assegnato
Route::get('/bordero/{workId}/pdf', [BorderoController::class, 'downloadPDF'])
    ->middleware('auth')
    ->name('bordero.pdf');

// Gestione borderò e catalogo pezzi lato admin (solo admin/sviluppatore)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/bordero/{workId}', [BorderoController::class, 'edit'])->name('admin.bordero.edit');
    Route::post('/admin/bordero/{workId}', [BorderoController::class, 'save'])->name('admin.bordero.save');

    Route::get('/admin/pezzi-bordero', [PezzoBorderoController::class, 'index'])->name('admin.pezzi-bordero.index');
    Route::get('/admin/pezzi-bordero/create', [PezzoBorderoController::class, 'create'])->name('admin.pezzi-bordero.create');
    Route::post('/admin/pezzi-bordero', [PezzoBorderoController::class, 'store'])->name('admin.pezzi-bordero.store');
    Route::get('/admin/pezzi-bordero/{id}/edit', [PezzoBorderoController::class, 'edit'])->name('admin.pezzi-bordero.edit');
    Route::put('/admin/pezzi-bordero/{id}', [PezzoBorderoController::class, 'update'])->name('admin.pezzi-bordero.update');
    Route::delete('/admin/pezzi-bordero/{id}', [PezzoBorderoController::class, 'destroy'])->name('admin.pezzi-bordero.destroy');
});

// Rotte per i report (dipendenti, lavori e clienti)
use App\Http\Controllers\ReportClientiController;
use App\Http\Controllers\ReportDipendentiController;
use App\Http\Controllers\ReportLavoriController;

Route::middleware(['auth'])->group(function () {
    Route::get('/reports/dipendenti', [ReportDipendentiController::class, 'index'])->name('reports.dipendenti.index');
    Route::post('/reports/dipendenti', [ReportDipendentiController::class, 'generate'])->name('reports.dipendenti.generate');
    Route::post('/reports/dipendenti/pdf', [ReportDipendentiController::class, 'pdf'])->name('reports.dipendenti.pdf');

    Route::get('/reports/lavori', [ReportLavoriController::class, 'index'])->name('reports.lavori.index');
    Route::post('/reports/lavori', [ReportLavoriController::class, 'generate'])->name('reports.lavori.generate');
    Route::post('/reports/lavori/pdf', [ReportLavoriController::class, 'pdf'])->name('reports.lavori.pdf');

    Route::get('/reports/clienti', [ReportClientiController::class, 'index'])->name('reports.clienti.index');
    Route::post('/reports/clienti', [ReportClientiController::class, 'generate'])->name('reports.clienti.generate');
    Route::post('/reports/clienti/pdf', [ReportClientiController::class, 'pdf'])->name('reports.clienti.pdf');
});

// Rotte per la gestione utenti (solo sviluppatori)
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckDeveloperRole;

Route::middleware(['auth', CheckDeveloperRole::class])->group(function () {
    Route::resource('users', UserController::class);
});
