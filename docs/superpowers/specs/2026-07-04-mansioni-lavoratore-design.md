# Mansioni Lavoratore — Design

## Contesto

Oggi ogni `Worker` può assumere qualsiasi `Work` non assegnato tramite "Assumi Lavoro" (`WorkerJobController::assumiLavoro`), indipendentemente dal `tipo_lavoro` (Trasporto, Smaltimento, Servizi). Serve introdurre il concetto di **mansione**: un worker può avere una o più mansioni tra `Trasportatore` e `Posatore`, e questo determina quali tipi di lavoro può assumere e vedere in dashboard.

Mapping mansione → tipi lavoro accessibili:
- `trasportatore` → `Trasporto`, `Smaltimento`
- `posatore` → `Servizi`

Un worker con entrambe le mansioni accede a tutti e 3 i tipi.

## Schema DB

Nuova tabella `worker_mansioni`:

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `worker_id` | FK → workers, cascade | |
| `mansione` | enum(`trasportatore`,`posatore`) | |
| `created_at`/`updated_at` | timestamps | |

Unique composite (`worker_id`, `mansione`) — evita duplicati.

**Migrazione dati esistenti**: ogni `Worker` già presente in DB riceve automaticamente riga `trasportatore` (default retroattivo, eseguito nella migration stessa via seeding inline).

## Model

`app/Models/WorkerMansione.php` — model minimale:
```php
class WorkerMansione extends Model
{
    protected $table = 'worker_mansioni';
    protected $fillable = ['worker_id', 'mansione'];
    public function worker() { return $this->belongsTo(Worker::class); }
}
```

`Worker` model, aggiunte:
```php
public function mansioni()
{
    return $this->hasMany(WorkerMansione::class);
}

public function hasMansione(string $mansione): bool
{
    return $this->mansioni->contains('mansione', $mansione);
}

public function tipiLavoroAccessibili(): array
{
    $tipi = [];
    if ($this->hasMansione('trasportatore')) {
        $tipi = array_merge($tipi, ['Trasporto', 'Smaltimento']);
    }
    if ($this->hasMansione('posatore')) {
        $tipi[] = 'Servizi';
    }
    return $tipi;
}
```

## Form Worker (create/edit)

`resources/views/workers/create.blade.php` e `edit.blade.php`: checkbox multiplo "Mansioni" (Trasportatore, Posatore), default checked "Trasportatore" su create. Nome campo `mansioni[]`.

`WorkerController::store()`: dopo creazione worker, sync mansioni da `$request->input('mansioni', ['trasportatore'])` (fallback trasportatore se nessuna spuntata, per sicurezza — anche se il default checkbox lo previene lato UI).

`WorkerController::update()`: sync mansioni da `$request->input('mansioni', [])` — qui NON si forza il default, l'admin può deliberatamente togliere tutte le mansioni se necessario (edge case accettato, worker semplicemente non vedrà/assumerà nulla finché non gliene assegna almeno una).

## Dashboard Lavoratore — filtro TAB

`WorkerJobController::index()`:
1. Calcola `$tipiAccessibili = $worker->tipiLavoroAccessibili()`.
2. Se vuoto → redirect con errore "Nessuna mansione assegnata. Contatta l'amministratore."
3. Query lavori non assegnati filtrata SEMPRE per `tipo_lavoro IN $tipiAccessibili` (mai lavori di tipo non accessibile, in nessun TAB).
4. TAB da mostrare: un TAB per ogni tipo in `$tipiAccessibili`; se `count($tipiAccessibili) > 1` aggiunge anche TAB "Tutti" (che mostra semplicemente l'unione, cioè la query base senza ulteriore filtro tipo).
5. Parametro query `?tab=Trasporto|Smaltimento|Servizi|tutti` per selezionare tab attivo, filtro applicato lato controller.

View `worker/jobs/index.blade.php`: nav-tabs Bootstrap sopra la tabella esistente, stessa tabella riusata per ogni tab (contenuto già filtrato dal controller, non serve JS client-side).

## Guardia assumiLavoro

`WorkerJobController::assumiLavoro($id)`: dopo il check "lavoro già assegnato", aggiunge:
```php
if (!in_array($work->tipo_lavoro, $worker->tipiLavoroAccessibili())) {
    return redirect()->route('worker.jobs')
        ->with('error', 'Mansione non abilitata per questo tipo di lavoro.');
}
```

Nessuna modifica a `show()`, `updateStatus()`, `storeSpesaLavoro()`, `storeIncassoLavoro()`, `storeMovimentoLavoro()` — restano invariati, dato che operano solo su lavori già assegnati al worker (impossibile arrivarci con tipo incompatibile, visto il gate su `assumiLavoro`).

## File da creare

- `database/migrations/2026_07_06_100000_create_worker_mansioni_table.php`
- `app/Models/WorkerMansione.php`
- `database/migrations/worker_mansioni.mysql.sql` (SQL MySQL manuale, come richiesto)

## File da modificare

- `app/Models/Worker.php` — relazione `mansioni()`, helper `hasMansione()`, `tipiLavoroAccessibili()`
- `app/Http/Controllers/WorkerController.php` — `store()`/`update()` sync mansioni
- `resources/views/workers/create.blade.php` — checkbox mansioni
- `resources/views/workers/edit.blade.php` — checkbox mansioni precompilate
- `app/Http/Controllers/WorkerJobController.php` — `index()` filtro tipi+tab, `assumiLavoro()` guardia
- `resources/views/worker/jobs/index.blade.php` — nav-tabs
- `docs/DATABASE.md`, `docs/OVERVIEW.md`, `docs/SEZIONI.md` — documentazione nuova feature

## Verifica

1. `php artisan migrate` → tabella creata, worker esistenti hanno riga `trasportatore`.
2. Crea worker nuovo senza toccare checkbox → mansione default Trasportatore salvata.
3. Modifica worker esistente, spunta anche Posatore → salva → verifica 2 righe in `worker_mansioni`.
4. Login worker solo Trasportatore → dashboard mostra TAB Trasporto + TAB Smaltimento (no TAB Tutti, no TAB Servizi) → lavori Servizi non appaiono in nessun tab.
5. Login worker con entrambe mansioni → dashboard mostra TAB Tutti + Trasporto + Smaltimento + Servizi.
6. Worker Posatore tenta URL diretto assumi-lavoro su un Lavoro Trasporto → redirect con errore, lavoro resta non assegnato.
7. Worker senza mansioni (edge case admin ha rimosso tutte) → redirect dashboard con errore "Nessuna mansione assegnata".
