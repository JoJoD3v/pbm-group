# PBM Group — Sezioni del Gestionale

Questa guida descrive ogni sezione funzionale, i percorsi, i controller e le view coinvolte.

---

## Autenticazione

| Rotta | Controller | View |
|---|---|---|
| `GET /login` | `LoginController@showLoginForm` | `login.blade.php` |
| `POST /login` | `LoginController@login` | — |
| `POST /logout` | `LoginController@logout` | — |
| `GET /register` | `UserRegistrationController@showRegistrationForm` | `register.blade.php` |
| `POST /register` | `UserRegistrationController@register` | — |

- La registrazione crea un `User`; lo sviluppatore assegna poi il ruolo.
- L'utente con ruolo `dipendente` accede a un'interfaccia completamente separata (`/worker/*`).

---

## Dashboard

**Rotta:** `GET /dashboard`  
**View:** `dashboard.blade.php`

- **Admin/Sviluppatore**: mostra i lavori (`works`) pianificati per oggi.
- **Dipendente**: mostra i propri lavori di oggi e il primo lavoro di domani.

---

## Sezione Admin

### 1. Lavori (`/works`)

**Controller:** `WorkController`  
**View:** `works/`

Gestione completa delle commesse di trasporto/smaltimento rifiuti.

| Azione | Rotta |
|---|---|
| Lista tutti | `GET /works` |
| Lista assegnati | `GET /works/assigned` |
| Lista non assegnati | `GET /works/unassigned` |
| Crea | `GET/POST /works/create` |
| Dettaglio | `GET /works/{id}` |
| Modifica | `GET/PUT /works/{id}/edit` |
| Elimina | `DELETE /works/{id}` |
| Crea smaltimento | `GET /works/create/disposal` |
| Depositi per materiale (AJAX) | `GET /works/deposits-by-material/{materialId}` |

Campi principali: `tipo_lavoro`, `customer_id`, `status_lavoro`, `data_esecuzione`, `costo_lavoro`, `modalita_pagamento`, punti partenza/destinazione con coordinate, `materiale`, `codice_eer`.

Filtri disponibili in lista: data inizio, data fine, tipo lavoro.

---

### 2. Assegnazione Lavori (`/work-assignments`)

**Controller:** `WorkAssignmentController`  
**View:** `admin/` (assegnamenti)

Associa uno o più `Worker` a un `Work` (tabella pivot `work_worker`).

| Azione | Rotta |
|---|---|
| Lista | `GET /work-assignments` |
| Form assegna | `GET /work-assignments/create` |
| Salva assegnazione | `POST /work-assignments` |
| Rimuovi assegnazione | `DELETE /work-assignments` |

---

### 3. Dipendenti (`/workers`)

**Controller:** `WorkerController`  
**View:** `workers/`

Anagrafica operatori. Resource completa (index, create, store, show, edit, update, destroy).

Campi: `id_worker`, `name_worker`, `cognome_worker`, `license_worker`, `worker_email`, `phone_worker`, `fondo_cassa`.

> **Attenzione:** `worker_email` deve coincidere con `users.email` per collegare il dipendente all'account di login.

---

### 4. Clienti (`/customers`)

**Controller:** `CustomerController`  
**View:** `customers/`

Resource completa. Supporta due tipi: `privato` (usa `full_name`) e `azienda` (usa `ragione_sociale`).

---

### 5. Materiali (`/materials`)

**Controller:** `MaterialController`  
**View:** `materials/`

Catalogo materiali/rifiuti con codice EER. Collegati ai depositi tramite `deposit_material`.

---

### 6. Depositi (`/deposits`)

**Controller:** `DepositController`  
**View:** `deposits/`

Discariche e siti di stoccaggio. Associati ai materiali che accettano con quantità (`deposit_material.quantity`).

---

### 7. Magazzini/Sedi (`/warehouses`)

**Controller:** `WarehouseController`  
**View:** `warehouses/`

Sedi aziendali con coordinate geografiche. Resource completa.

---

### 8. Automezzi (`/vehicles`)

**Controller:** `VehicleController`  
**View:** `vehicles/`

Parco veicoli con targa e scadenza assicurazione. Resource completa.

---

### 9. Assegnazione Automezzi (`/vehicle-assignments`)

**Controller:** `VehicleAssignmentController` / `VehicleAssignmentReportController`  
**View:** `admin/` (assegnamenti veicoli)

Assegna veicoli ai dipendenti con data assegnazione e note. La tabella pivot `vehicle_worker` tiene traccia delle assegnazioni attive (nessuna `data_restituzione`) e storiche.

| Azione | Rotta |
|---|---|
| Lista assegnazioni | `GET /vehicle-assignments` |
| Form assegna | `GET /vehicle-assignments/create` |
| Salva | `POST /vehicle-assignments` |
| Modifica | `GET/PUT /vehicle-assignments/{vehicle}/{worker}/edit` |
| Rimuovi | `DELETE /vehicle-assignments/{vehicle}/{worker}` |
| Report storico | `GET /vehicle-assignments/report` |
| Elimina record storico | `DELETE /vehicle-assignments/report/{id}` |

---

### 10. Carte Prepagate (`/credit-cards`)

**Controller:** `CreditCardController`  
**View:** `credit_cards/`

Gestione carte prepagate aziendali (es. DKV). Resource completa.  
Campi: `numero_carta` (cifrato), `scadenza_carta`, `fondo_carta`.

---

### 11. Assegnazione Carte (`/credit-card-assignments`)

**Controller:** `CreditCardAssignmentController`  
**View:** `credit_card_assignments/`

Assegna una carta a un dipendente (tabella `credit_card_worker`). Resource completa.

---

### 12. Ricariche Carte (`/credit-card-recharges`)

**Controller:** `CreditCardRechargeController`  
**View:** `credit_card_recharges/`

Registra ricariche sul `fondo_carta` delle carte prepagate. Resource completa.

---

### 13. Fondo Cassa — Gestione Saldi (`/admin/fondo-cassa`)

**Controller:** `AdminFondoCassaController`  
**View:** `admin/cashflow/fondo_cassa_list.blade.php`, `admin/cashflow/fondo_cassa_edit.blade.php`  
**Menu:** Fondo Cassa → "Fondo Cassa Dipendenti"

Lista tutti i dipendenti con il loro saldo `fondo_cassa` attuale. Il CTA "Modifica" apre un form dove l'admin imposta il **nuovo valore assoluto** del fondo. Il sistema:
1. Calcola la differenza (nuovo − vecchio).
2. Aggiorna `workers.fondo_cassa`.
3. Crea un `CashMovement` con `motivo = "Modifica da Admin"` (+ eventuale nota).
4. Il tipo (`entrata`/`uscita`) dipende dal segno della differenza.

L'operazione è in transazione DB.

| Rotta | Azione |
|---|---|
| `GET /admin/fondo-cassa` | Lista dipendenti + saldi |
| `GET /admin/fondo-cassa/{worker}/edit` | Form modifica |
| `PUT /admin/fondo-cassa/{worker}` | Salva modifica |

---

### 14. Fondo Cassa — Ricarica (`/worker/cash/recharge`)

**Controller:** `WorkerCashRechargeController`  
**View:** `admin/cashflow/recharge.blade.php`  
**Menu:** Fondo Cassa → "Ricarica Fondo Cassa"

Aggiunge un importo fisso al `fondo_cassa` del dipendente selezionato. Crea sempre un `CashMovement` di tipo `entrata` con motivo `"Ricarica fondo cassa: <note>"`.

---

### 15. Fondo Cassa — Report Movimenti (`/reports/cashflow`)

**Controller:** `CashMovementReportController`  
**View:** `admin/reports/cashflow/index.blade.php`, `admin/reports/cashflow/report.blade.php`  
**Menu:** Fondo Cassa → "Report Movimenti"

Genera un report filtrato per dipendente e **range di date** (`data_inizio` / `data_fine`).

Il report mostra:
- Totale entrate, uscite, saldo del periodo
- Tabella movimenti con: Data, Ora, Tipo (badge), Importo, Metodo pagamento, Motivazione, Riferimento (link a Lavoro o Carta se presente)

La query usa `whereBetween('data_movimento', [$dataInizio, $dataFine])`.

---

### 16. Utenti (`/users`) — Solo sviluppatore

**Controller:** `UserController`  
**View:** `users/`

Gestione account utenti (solo per ruolo `sviluppatore`). Permette creare, modificare, eliminare utenti e inviare le credenziali via email (`UserCredentialsMail`).

---

## Sezione Dipendente (`/worker/*`)

Tutte le rotte sono protette da `CheckWorkerRole` (blocca chi non è `dipendente`).

### 1. Fondo Cassa Dipendente (`/worker/cashflow`)

**Controller:** `WorkerCashFlowController@index`  
**View:** `worker/cashflow/index.blade.php`

Il dipendente vede i propri movimenti del giorno (filtrabile per data tramite datepicker) con totali entrate/uscite e il saldo attuale del fondo cassa.

---

### 2. Registra Spesa (`/worker/cashflow/spesa`)

**Controller:** `WorkerCashFlowController@createSpesa` / `storeSpesa`  
**View:** `worker/cashflow/spesa.blade.php`

Il dipendente registra un'uscita dal fondo cassa. Può pagare con:
- **Contanti** (scala da `fondo_cassa`)
- **DKV** (scala da `fondo_carta` della carta assegnata)
- **Carta prepagata** (scala da `fondo_carta` della carta assegnata)

Crea un `CashMovement` di tipo `uscita`.

---

### 3. Registra Incasso (`/worker/cashflow/incasso`)

**Controller:** `WorkerCashFlowController@createIncasso` / `storeIncasso`  
**View:** `worker/cashflow/incasso.blade.php`

Il dipendente registra un'entrata (es. pagamento ricevuto da cliente). Crea un `CashMovement` di tipo `entrata`, collegabile a un `Work`.

---

### 4. Lista Lavori Dipendente (`/worker/jobs`)

**Controller:** `WorkerJobController`  
**View:** `worker/jobs/`

Il dipendente vede i lavori a lui assegnati. Può vedere il dettaglio (`/worker/jobs/{id}`) e aggiornare lo status del lavoro.

---

### 5. Carte Dipendente (`/worker/cards`)

**Controller:** `WorkerCardController`  
**View:** `worker/`

Il dipendente vede le carte prepagate assegnate al suo profilo e il relativo saldo.

---

### 6. Ricevute (`/worker/ricevute`)

**Controller:** `RicevutaController`  
**View:** `worker/ricevute/`

Al termine di un lavoro il dipendente può generare una ricevuta con firma digitale (base64), dati pagamento e foto bolla. La ricevuta viene generata in PDF tramite **Dompdf** e può essere scaricata.
