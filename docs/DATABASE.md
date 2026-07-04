# PBM Group — Schema del Database

## Tabelle principali

### `users`
Utenti che accedono al gestionale.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `first_name` | string | |
| `last_name` | string | |
| `email` | string unique | Coincide con `worker_email` per collegare User ↔ Worker |
| `role` | string | `sviluppatore`, `amministratore`, `dipendente` |
| `phone` | string nullable | |
| `password` | string hashed | |

---

### `workers`
Anagrafica dipendenti operativi.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `id_worker` | string unique | Codice identificativo interno |
| `name_worker` | string | Nome |
| `cognome_worker` | string | Cognome |
| `license_worker` | string | Patente |
| `worker_email` | string unique | **Chiave di collegamento con `users.email`** |
| `phone_worker` | string nullable | |
| `fondo_cassa` | decimal(10,2) | Saldo contanti disponibili al dipendente |

**Relazione con User**: `User hasOne Worker` tramite `users.email = workers.worker_email`

---

### `worker_mansioni`
Mansioni assegnate a ciascun lavoratore (many, tramite pivot). Determinano quali `tipo_lavoro` il worker può assumere/vedere in dashboard.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `worker_id` | FK → workers, cascade | |
| `mansione` | enum(`trasportatore`,`posatore`) | Unique insieme a `worker_id` |

Mapping mansione → tipi lavoro accessibili (`Worker::tipiLavoroAccessibili()`):
- `trasportatore` → `Trasporto`, `Smaltimento`
- `posatore` → `Servizi`

> Worker esistenti al momento dell'introduzione di questa feature hanno ricevuto automaticamente la mansione `trasportatore` (backfill preservato in `2026_07_10_000014_consolidated_worker_mansioni_table.php`). Nuovi worker creati da form hanno `trasportatore` pre-selezionato di default, ma l'admin può cambiare la selezione.

---

### `works`
Lavori (commesse) assegnati ai clienti o appaltatori.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `tipo_lavoro` | string | `Trasporto`, `Smaltimento`, `Servizi` |
| `customer_id` | FK → customers, nullable | Nullable perché un Lavoro Servizi può usare `appaltatore_id` al suo posto |
| `appaltatore_id` | FK → appaltatori, nullable | Valorizzato solo per lavori con committente Appaltatore (mutuamente esclusivo con `customer_id`, vincolo applicativo non DB) |
| `status_lavoro` | string | Default `In Sospeso` |
| `data_esecuzione` | datetime nullable | |
| `costo_lavoro` | decimal(10,2) nullable | Per Lavoro Servizi: somma righe `work_servizi` + eventuale costo extra manuale |
| `modalita_pagamento` | string nullable | |
| `nome_partenza` | string nullable | |
| `indirizzo_partenza` | string nullable | Per Lavoro Servizi riusato come unico "luogo intervento" |
| `latitude_partenza` | decimal(10,7) nullable | |
| `longitude_partenza` | decimal(10,7) nullable | |
| `materiale` | string nullable | Nome materiale trasportato (solo Trasporto/Smaltimento) |
| `codice_eer` | string nullable | Codice EER rifiuto (solo Trasporto/Smaltimento) |
| `nome_destinazione` | string nullable | Non usato da Lavoro Servizi |
| `indirizzo_destinazione` | string nullable | Non usato da Lavoro Servizi |
| `latitude_destinazione` | decimal(10,7) nullable | |
| `longitude_destinazione` | decimal(10,7) nullable | |

> `customer_id`, `nome_destinazione`, `indirizzo_destinazione` sono nullable (necessario per Lavoro Servizi, che usa `appaltatore_id` al posto di `customer_id` e non valorizza i campi destinazione). Schema consolidato in `2026_07_10_000004_consolidated_works_table.php`.

---

### `customers`
Clienti committenti dei lavori.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `customer_type` | string | `privato` / `azienda` |
| `full_name` | string | Per privati |
| `codice_fiscale` | string nullable | |
| `ragione_sociale` | string nullable | Per aziende |
| `partita_iva` | string nullable | |
| `address` | string | |
| `phone` | string | |
| `email` | string | |
| `latitude_customer` | decimal nullable | |
| `longitude_customer` | decimal nullable | |

---

### `appaltatori`
Appaltatori (controparte contrattuale diversa dal cliente finale). Mirror strutturale di `customers`.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `tipo_soggetto` | enum(`fisica`,`giuridica`) | |
| `full_name` | string nullable | Per persona fisica |
| `codice_fiscale` | string nullable | |
| `ragione_sociale` | string nullable | Per persona giuridica |
| `partita_iva` | string nullable | |
| `address` | string nullable | |
| `phone` | string nullable | |
| `email` | string nullable | |
| `latitude_appaltatore` | decimal(10,7) nullable | |
| `longitude_appaltatore` | decimal(10,7) nullable | |
| `note` | text nullable | |

CRUD completo via `AppaltatoreController` (mirror di `CustomerController`), viste in `resources/views/appaltatori/`.

---

### `services`
Catalogo servizi offerti (usato da Lavoro Servizi).

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `nome_servizio` | string | |
| `prezzo_servizio` | decimal(10,2) | Prezzo unitario di listino |

---

### `work_servizi`
Righe servizio selezionate per un Lavoro di tipo "Servizi". Prezzo e nome sono uno **snapshot** copiato da `services` al momento del salvataggio: sopravvivono a modifiche/cancellazioni successive del catalogo.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `work_id` | FK → works, cascade | |
| `service_id` | FK → services, nullable, `set null` | Link opzionale al catalogo; **non usare per la visualizzazione** |
| `nome_servizio` | string | Snapshot, sempre usato per display |
| `prezzo_unitario` | decimal(10,2) | Snapshot |
| `quantita` | integer default 1 | |
| `iva_applicata` | boolean default false | Se true, subtotale riga ×1.22 |

---

### `pezzi_bordero`
Catalogo condiviso dei "pezzi" (materiali/parti) utilizzabili nei Borderò. CRUD completo (`PezzoBorderoController`, admin-only) — edit/destroy non impattano righe storiche già registrate (vedi `bordero_pezzi`).

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `nome_pezzo` | string unique | |

---

### `bordero`
Borderò: una scheda pezzi/materiali per Lavoro (1:1 con `works`, solo per `tipo_lavoro = 'Servizi'`), compilata dal dipendente assegnato e modificabile anche da admin tramite la stessa view/form.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `work_id` | FK → works, unique, cascade | Un solo Borderò per Lavoro |
| `worker_id` | FK → workers, nullable, `set null` | Chi ha compilato/aggiornato per ultimo |
| `status` | string default `In Sospeso` | `Completo` / `In Sospeso` / `Non realizzabile` |
| `note_tecniche` | text nullable | |

---

### `bordero_pezzi`
Righe pezzo/quantità di un Borderò. Stesso pattern di snapshot di `work_servizi`: `nome_pezzo` è copiato da `pezzi_bordero` al salvataggio, `pezzo_bordero_id` è un link opzionale nullable — la cancellazione/rename di un pezzo dal catalogo non altera le righe storiche.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `bordero_id` | FK → bordero, cascade | |
| `pezzo_bordero_id` | FK → pezzi_bordero, nullable, `set null` | Link opzionale al catalogo; **non usare per la visualizzazione** |
| `nome_pezzo` | string | Snapshot, sempre usato per display |
| `quantita` | integer default 1 | |

Ad ogni salvataggio del form Borderò, le righe esistenti vengono cancellate e ricreate (strategia "replace-all", non diff per riga).

---

### `vehicles`
Automezzi aziendali.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `nome` | string | Nome/modello |
| `targa` | string | |
| `scadenza_assicurazione` | date | |

---

### `materials`
Tipologie di materiali/rifiuti gestiti.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | |
| `eer_code` | string | Codice EER europeo |

---

### `deposits`
Depositi/discariche autorizzate a ricevere i materiali.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | |
| `address` | string | |
| `latitude` | decimal nullable | |
| `longitude` | decimal nullable | |

---

### `warehouses`
Sedi/magazzini aziendali.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `nome_sede` | string | |
| `indirizzo` | string | |
| `latitude_warehouse` | decimal nullable | |
| `longitude_warehouse` | decimal nullable | |

---

### `credit_cards`
Carte prepagate aziendali distribuite ai dipendenti.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `numero_carta` | text | (cifrato/offuscato) |
| `scadenza_carta` | date | |
| `fondo_carta` | decimal(10,2) | Saldo disponibile sulla carta |

---

### `cash_movements`
Movimenti del fondo cassa di ciascun dipendente (entrate e uscite).

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `worker_id` | FK → workers | |
| `work_id` | FK → works nullable | Lavoro collegato |
| `tipo_movimento` | string | `entrata` / `uscita` |
| `importo` | decimal(10,2) | Sempre positivo |
| `motivo` | string | Descrizione libera |
| `metodo_pagamento` | string | `contanti`, `dkv`, `carta` |
| `credit_card_id` | FK → credit_cards nullable | Se pagato con carta |
| `data_movimento` | date | Giorno del movimento |

**Movimenti generati automaticamente dal sistema:**
- Ricarica admin → `entrata`, motivo `"Ricarica fondo cassa: <note>"`
- Modifica saldo admin → `entrata` o `uscita`, motivo `"Modifica da Admin"` (± nota)
- Spesa dipendente → `uscita`
- Incasso dipendente → `entrata`

---

### `ricevute`
Ricevute di pagamento generate dai dipendenti al termine di un lavoro.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `work_id` | FK → works | |
| `numero_ricevuta` | string | Generato automaticamente |
| `fattura` | boolean | |
| `riserva_controlli` | boolean | |
| `nome_ricevente` | string | |
| `firma_base64` | text | Firma digitale |
| `pagamento_effettuato` | boolean | |
| `somma_pagamento` | decimal nullable | |
| `foto_bolla` | string nullable | Path file |

---

### `vehicle_assignment_logs`
Storico assegnazioni/restituzioni veicoli ai dipendenti (log, distinto dalla pivot `vehicle_worker` che tiene l'assegnazione corrente).

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `vehicle_id` | FK → vehicles, cascade | |
| `worker_id` | FK → workers, cascade | |
| `data_assegnazione` | datetime | |
| `data_restituzione` | datetime nullable | |
| `note` | text nullable | |
| `operazione` | string | `assegnazione` / `restituzione` |

---

### `credit_card_recharges`
Storico ricariche di una carta prepagata. Nessun Eloquent model dedicato — acceduta via `DB::table()` in `CreditCardRechargeController`.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `credit_card_id` | FK → credit_cards, cascade | |
| `user_id` | FK → users, nullable, cascade | Chi ha effettuato la ricarica |
| `importo` | decimal(10,2) | |
| `data_ricarica` | datetime | |
| `note` | text nullable | |

---

## Tabelle pivot

| Tabella | Collega | Campi extra |
|---|---|---|
| `work_worker` | works ↔ workers | — |
| `vehicle_worker` | vehicles ↔ workers | `data_assegnazione`, `data_restituzione` (nullable), `note` |
| `credit_card_worker` | credit_cards ↔ workers | — |
| `deposit_material` | deposits ↔ materials | `quantity` |

---

## Relazioni chiave (Eloquent)

```
User         hasOne    Worker          (users.email = workers.worker_email)
Worker       hasMany   WorkerMansione  (mansioni())
WorkerMansione belongsTo Worker
Work         belongsTo Customer (nullable)
Work         belongsTo Appaltatore (nullable)
Work         belongsToMany Workers     (work_worker)
Work         hasMany   Ricevute
Work         hasMany   WorkServizio  (servizi())
Work         hasOne    Bordero
Worker       belongsToMany Works       (work_worker)
Worker       belongsToMany Vehicles    (vehicle_worker)
Worker       belongsToMany CreditCards (credit_card_worker, no data_restituzione)
Worker       hasMany   CashMovements
Material     belongsToMany Deposits    (deposit_material)
CashMovement belongsTo Worker
CashMovement belongsTo Work (nullable)
CashMovement belongsTo CreditCard (nullable)
WorkServizio belongsTo Work
WorkServizio belongsTo Service (nullable, solo link opzionale — display usa sempre nome_servizio/prezzo_unitario snapshot)
Bordero      belongsTo Work
Bordero      belongsTo Worker (nullable)
Bordero      hasMany   BorderoPezzo (pezzi())
BorderoPezzo belongsTo Bordero
BorderoPezzo belongsTo PezzoBordero (nullable, solo link opzionale — display usa sempre nome_pezzo snapshot)
PezzoBordero hasMany   BorderoPezzo  (righeBordero())
```
