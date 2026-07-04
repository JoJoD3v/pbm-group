# Appaltatori + Lavoro Servizi — Design

## Contesto

Il gestionale gestisce già Clienti e due tipi di Lavoro (Trasporto, Smaltimento). Serve estendere con:
1. Un nuovo soggetto "Appaltatore" (controparte contrattuale diversa dal Cliente finale), con anagrafica gestita separatamente.
2. Un terzo tipo di Lavoro, "Lavoro Servizi", che non trasporta materiale ma fattura una selezione di servizi a catalogo (già esistenti in `services`) a un Cliente o a un Appaltatore.
3. Il sistema Borderò (già implementato) va vincolato: la CTA di creazione/visualizzazione deve comparire solo sui Lavori di tipo "Servizi", non più su tutti i lavori assegnati a un worker.

## 1. Appaltatori

Nuovo model `Appaltatore`, mirror esatto di `Customer` (stessa struttura fisica/giuridica), con controller CRUD e viste proprie — non riusa la tabella `customers`.

**Migration** `appaltatori`:
```php
Schema::create('appaltatori', function (Blueprint $table) {
    $table->id();
    $table->string('tipo_soggetto'); // fisica | giuridica
    $table->string('full_name')->nullable();
    $table->string('ragione_sociale')->nullable();
    $table->string('codice_fiscale')->nullable();
    $table->string('partita_iva')->nullable();
    $table->string('address')->nullable();
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->decimal('latitude_appaltatore', 10, 7)->nullable();
    $table->decimal('longitude_appaltatore', 10, 7)->nullable();
    $table->text('note')->nullable();
    $table->timestamps();
});
```

**Model** `app/Models/Appaltatore.php` — stesso `$fillable` shape di `Customer`.

**Controller** `AppaltatoreController` — copia 1:1 di `CustomerController` (stessa validazione condizionale fisica/giuridica), redirect verso `appaltatori.index`.

**Viste** `resources/views/appaltatori/{index,create,edit,show}.blade.php` — copia di `customers/*` con testo/label "Appaltatore" al posto di "Cliente".

**Route**: `Route::resource('appaltatori', AppaltatoreController::class)->middleware('auth');`

**Sidebar**: nuova `<li class="nav-item">` "Appaltatori" subito dopo la voce "Clienti" in `partials/sidebar.blade.php`, stesso stile flat (icona `bi-person-badge` o simile, non `bi-person` già usata da Clienti).

## 2. Schema dati Lavoro Servizi

**Migration** aggiunge a `works`:
```php
$table->unsignedBigInteger('appaltatore_id')->nullable()->after('customer_id');
$table->foreign('appaltatore_id')->references('id')->on('appaltatori')->onDelete('set null');
$table->unsignedBigInteger('customer_id')->nullable()->change();
```
Confermato: `customer_id` è oggi `NOT NULL` a DB (`2025_05_26_000003_consolidated_works_table.php`). Va reso nullable per permettere lavori Servizi con solo `appaltatore_id` valorizzato. `doctrine/dbal` è già in `composer.json` (`^4.2`), quindi `->change()` funziona senza dipendenze aggiuntive.

Nessun nuovo campo indirizzo: "Lavoro Servizi" riusa `indirizzo_partenza` + `latitude_partenza`/`longitude_partenza` esistenti come unico "luogo intervento" (destinazione, materiale, deposit/warehouse restano `null` per questo tipo — semplicemente non popolati dal form).

**Nuova tabella** `work_servizi` (denormalizzata come `bordero_pezzi`, per sopravvivere a modifiche future del catalogo `services`):
```php
Schema::create('work_servizi', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('work_id');
    $table->unsignedBigInteger('service_id')->nullable();
    $table->string('nome_servizio');
    $table->decimal('prezzo_unitario', 10, 2);
    $table->integer('quantita')->default(1);
    $table->boolean('iva_applicata')->default(false);
    $table->timestamps();

    $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
    $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
});
```

**Model** `WorkServizio` — `belongsTo(Work::class)`, `belongsTo(Service::class)`. Visualizzazione usa sempre `nome_servizio`/`prezzo_unitario` (snapshot), mai il valore live da `service()`.

**Work model** — aggiungere:
```php
public function appaltatore() { return $this->belongsTo(Appaltatore::class); }
public function servizi() { return $this->hasMany(WorkServizio::class); }
```

Vincolo "esattamente uno tra Cliente/Appaltatore" è solo applicativo (validato in `WorkController::store`), non un DB constraint — coerente con lo stile del resto dello schema (nessun check constraint usato altrove in questo progetto).

## 3. Form Creazione Lavoro Servizi

Nuova route `GET /works/create/servizi` → `WorkController::createServizi()` (mirror di `createDisposal()`), carica `customers`, `appaltatori`, `services`. Vista `resources/views/works/create_servizi.blade.php`, link sidebar dopo "Lavoro Smaltimento".

Form (estende pattern di `works/create.blade.php`):
- `tipo_lavoro` hidden = "Servizi"
- Radio "Cliente" / "Appaltatore" → mostra/nasconde select `customer_id` o `appaltatore_id` corrispondente (stesso toggle JS di `materiale_option`)
- Se Cliente selezionato: select "Indirizzo Cliente" / "Indirizzo Libero" → popola `indirizzo_partenza` (readonly da dati cliente, o Google Places autocomplete se libero — riuso funzioni JS esistenti `updateIndirizzoPartenza`)
- Se Appaltatore selezionato: campo indirizzo disabilitato con placeholder "Vedi Note"
- Righe dinamiche Servizi (stesso pattern `<template>`+clone JS di `bordero/form.blade.php`): ogni riga ha select `servizi[i][service_id]` (da catalogo `services`, `data-prezzo`), input `quantita`, checkbox `iva_applicata` per riga. JS calcola subtotale riga (prezzo × qty × 1.22 se iva) e totale complessivo live, mostrato readonly
- Campo `costo_lavoro` manuale opzionale (si somma al totale calcolato dalle righe servizio)
- Select `modalita_pagamento` (stesse opzioni di Trasporto/Smaltimento)
- Textarea `note`

**Controller `store()`**: quando `tipo_lavoro === 'Servizi'`, validazione dedicata:
```php
'customer_id' => 'required_without:appaltatore_id|nullable|exists:customers,id',
'appaltatore_id' => 'required_without:customer_id|nullable|exists:appaltatori,id',
'servizi' => 'required|array|min:1',
'servizi.*.service_id' => 'required|exists:services,id',
'servizi.*.quantita' => 'required|integer|min:1',
'servizi.*.iva_applicata' => 'nullable|boolean',
```
Rifiuta esplicitamente se sia `customer_id` che `appaltatore_id` sono valorizzati (check manuale post-validazione, redirect back con errore).

In `DB::transaction`: crea `Work` (con `appaltatore_id`, `indirizzo_partenza`/lat/lon, niente destinazione/materiale), poi per ogni riga crea `WorkServizio` con snapshot `nome_servizio`/`prezzo_unitario` da `Service::find($riga['service_id'])`, calcola `costo_lavoro` = somma(prezzo×qty×iva-factor per riga) + `costo_lavoro` manuale opzionale.

`works/index.blade.php` — colonna Tipo già mostra `$work->tipo_lavoro` direttamente, "Servizi" appare automaticamente, nessuna modifica necessaria.

## 4. Vincolo CTA Borderò a Lavoro Servizi

- `resources/views/worker/jobs/show.blade.php`: il blocco esistente con CTA "Creazione Borderò"/"Vedi/Modifica Borderò" viene avvolto in `@if($work->tipo_lavoro === 'Servizi')`.
- `resources/views/works/show.blade.php`: la card "Borderò" (admin) viene avvolta nello stesso `@if($work->tipo_lavoro === 'Servizi')`.
- Nessuna modifica a `BorderoController` — resta agnostico al tipo lavoro (già funziona per qualunque `work_id`); il gate è solo a livello vista.

## File da creare

- `database/migrations/2026_07_05_100000_create_appaltatori_table.php`
- `database/migrations/2026_07_05_100001_add_appaltatore_to_works_and_work_servizi.php`
- `app/Models/Appaltatore.php`
- `app/Models/WorkServizio.php`
- `app/Http/Controllers/AppaltatoreController.php`
- `resources/views/appaltatori/{index,create,edit,show}.blade.php`
- `resources/views/works/create_servizi.blade.php`

## File da modificare

- `app/Models/Work.php` — relazioni `appaltatore()`, `servizi()`
- `app/Http/Controllers/WorkController.php` — `createServizi()`, estensione `store()` per ramo Servizi
- `routes/web.php` — `Route::resource('appaltatori', ...)`, `GET /works/create/servizi`
- `resources/views/partials/sidebar.blade.php` — voce "Appaltatori" dopo Clienti, voce "Lavoro Servizi" dopo "Lavoro Smaltimento"
- `resources/views/worker/jobs/show.blade.php` — gate CTA Borderò a `tipo_lavoro === 'Servizi'`
- `resources/views/works/show.blade.php` — gate card Borderò a `tipo_lavoro === 'Servizi'`

## Verifica

1. `php artisan migrate` → tabelle `appaltatori`, `work_servizi`, colonna `appaltatore_id` su `works`.
2. Sidebar → "Appaltatori" → crea 1 fisica + 1 giuridica, verifica CRUD completo.
3. Sidebar → Lavori → "Lavoro Servizi" → seleziona Cliente → verifica indirizzo auto-popolato/libero.
4. Stesso form → seleziona Appaltatore → verifica campo indirizzo disabilitato "Vedi Note".
5. Aggiungi 2-3 righe servizio da catalogo, quantità diverse, IVA su una riga sì una no → verifica totale calcolato live.
6. Submit → verifica `Work` creato con `tipo_lavoro='Servizi'`, righe in `work_servizi` con snapshot corretto, `costo_lavoro` corretto.
7. Vista `works.index` → colonna Tipo mostra "Servizi".
8. Scheda admin lavoro Servizi → card Borderò visibile. Scheda lavoro Trasporto/Smaltimento esistente → card Borderò assente.
9. Login worker assegnato a Lavoro Servizi → CTA Borderò visibile. Worker assegnato a Lavoro Trasporto → CTA assente.
10. Rinomina/elimina un servizio dal catalogo `services` → riga storica in `work_servizi` di un lavoro già creato mostra ancora nome/prezzo originali (snapshot).
