# PBM Group — Regole per Coding Agent

Questo file definisce le linee guida che ogni coding agent deve seguire quando lavora su questo progetto. Leggi sempre questo file prima di fare qualsiasi modifica.

---

## Prima di modificare qualcosa

1. **Leggi i file di documentazione** in `docs/`:
   - `OVERVIEW.md` — stack, ruoli, convenzioni generali
   - `DATABASE.md` — schema completo delle tabelle e relazioni
   - `SEZIONI.md` — dettaglio funzionale di ogni sezione
2. **Leggi il file che stai per modificare** prima di toccare qualsiasi cosa. Non assumere mai il contenuto di un file senza averlo letto.
3. **Usa la ricerca** (`grep_search`, `semantic_search`, `file_search`) per localizzare il codice rilevante invece di riscrivere da zero.

---

## Convenzioni obbligatorie

### Date
- Nel DB le date sono sempre in formato **`YYYY-MM-DD`** (ISO 8601).
- In UI le date sono sempre visualizzate in formato **`DD/MM/YYYY`** (italiano).
- Tutti gli `input[type="date"]` vengono intercettati da `public/js/date-picker-manager.js` (Flatpickr con locale IT).
- **Bug noto già risolto**: Flatpickr riceve un oggetto `Date` JavaScript (non la stringa ISO) per evitare il parsing errato con `dateFormat: 'd/m/Y'`. Non tornare alla stringa grezza.
- Per passare `defaultDate` a flatpickr: costruire sempre un `new Date(year, month-1, day)` dal valore ISO.

### Moneta
- Usa sempre `number_format($valore, 2, ',', '.')` nelle view Blade per formattare importi.

### Flash messages
- `session('success')` — operazione completata.
- `session('error')` — errore.
- `session('info')` — avviso neutro.
- Ogni view che esegue azioni deve mostrare questi tre tipi di messaggio nella sezione `@section('content')`.

### Ruoli
- Controlla sempre il ruolo con `Auth::user()->role` nei controller.
- I valori possibili sono: `sviluppatore`, `amministratore`, `dipendente`.
- Il ruolo `dipendente` non deve mai accedere a rotte admin — usa `return redirect()->route('dashboard')->with('error', ...)`.
- Le rotte `/worker/*` sono protette da `CheckWorkerRole` middleware.

### Fondo Cassa
- Ogni modifica a `workers.fondo_cassa` **deve** creare un record in `cash_movements`.
- Usare sempre `DB::beginTransaction()` / `DB::commit()` / `DB::rollBack()` quando si modificano sia `workers` che `cash_movements` insieme.
- Il campo `importo` in `cash_movements` è **sempre positivo**; il tipo (`entrata`/`uscita`) indica la direzione.
- `motivo` deve essere descrittivo: indica la causa (es. `"Ricarica fondo cassa: rifornimento"`, `"Modifica da Admin: correzione saldo"`).

---

## Aggiungere una nuova funzionalità

### Checklist

- [ ] Il controller è in `app/Http/Controllers/`
- [ ] Le rotte sono in `routes/web.php` con `use` statement **fuori** da qualsiasi closure Route
- [ ] Le view sono in `resources/views/admin/` (se admin) o `resources/views/worker/` (se dipendente)
- [ ] Il link è stato aggiunto nella sidebar corretta (`partials/sidebar.blade.php` o `partials/worker_sidebar.blade.php`)
- [ ] Le validazioni Request usano messaggi di errore in italiano
- [ ] Le operazioni multi-tabella usano transazioni DB
- [ ] Le date nel `value` degli input sono in formato ISO, non in formato italiano

### Pattern rotte

```php
// Correct: use statement OUTSIDE any Route group/closure
use App\Http\Controllers\MyController;
Route::middleware(['auth'])->group(function () {
    Route::get('/path', [MyController::class, 'method'])->name('route.name');
});
```

**Mai** mettere `use App\...` dentro una closure di Route — è PHP non valido.

### Pattern controller standard

```php
public function store(Request $request)
{
    if (Auth::user()->role === 'dipendente') {
        return redirect()->route('dashboard')->with('error', 'Accesso non autorizzato.');
    }

    $request->validate([...], [.../* messaggi IT */]);

    DB::beginTransaction();
    try {
        // operazioni
        DB::commit();
        return redirect()->route('...')->with('success', 'Operazione completata.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Errore: ' . $e->getMessage());
    }
}
```

---

## Struttura Sidebar

La sidebar admin (`partials/sidebar.blade.php`) è organizzata in sezioni con collapse Bootstrap 4.  
Ogni sezione è una `<li class="nav-item">` con:
- `data-toggle="collapse"` e `data-target="#collapseXxx"`
- Un div `#collapseXxx` con class `collapse` contenente i link

Quando aggiungi una nuova voce a una sezione esistente, inserisci il link come `<a class="collapse-item" href="...">`.

---

## Cosa NON fare

- **Non modificare le migration consolidate** in `2025_05_26_*`. Crea nuove migration se occorrono modifiche allo schema.
- **Non usare `dd()` o `var_dump()` in produzione** — rimuovili sempre prima di fare commit.
- **Non hardcodare valori di ruolo** al di fuori dei controller. Se la logica ruoli si espande, considera un Gate/Policy Laravel.
- **Non saltare la validazione** degli input utente, specialmente per importi e date.
- **Non creare file di documentazione** aggiuntivi dopo ogni modifica, a meno che non sia esplicitamente richiesto.
- **Non usare `&&` per concatenare comandi PowerShell** — usa `;` al posto di `&&`.

---

## Riferimento rapido: nomi route principali

| Sezione | Route name |
|---|---|
| Dashboard | `dashboard` |
| Lavori lista | `works.index` |
| Lavori assegnati | `works.assigned` |
| Lavori non assegnati | `works.unassigned` |
| Assegna lavoro | `work.assignments.store` |
| Dipendenti | `workers.index` |
| Clienti | `customers.index` |
| Materiali | `materials.index` |
| Depositi | `deposits.index` |
| Magazzini | `warehouses.index` |
| Automezzi | `vehicles.index` |
| Assegna automezzo | `vehicle.assignments.store` |
| Report automezzi | `vehicle.assignments.report` |
| Carte prepagate | `credit-cards.index` |
| Assegna carta | `credit-card-assignments.index` |
| Ricarica carta | `credit-card-recharges.create` |
| Fondo Cassa — Saldi | `admin.fondo-cassa.index` |
| Fondo Cassa — Ricarica | `worker.cash.recharge` |
| Fondo Cassa — Report | `reports.cashflow.index` |
| Worker: lavori | `worker.jobs` |
| Worker: fondo cassa | `worker.cashflow` |
| Worker: spesa | `worker.cashflow.spesa.create` |
| Worker: incasso | `worker.cashflow.incasso.create` |
| Worker: ricevuta | `worker.ricevute.create` |
| Worker: carte | `worker.cards` |

---

## Ambiente di sviluppo

- OS: Windows — usare PowerShell, comandi separati con `;` non `&&`
- Server: `php artisan serve` (porta 8000 default)
- Asset: `npm run dev`
- Cache: se ci sono comportamenti strani dopo modifiche a config/route, eseguire:
  ```
  php artisan config:clear; php artisan route:clear; php artisan view:clear; php artisan cache:clear
  ```
