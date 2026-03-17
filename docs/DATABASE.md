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

### `works`
Lavori (commesse) assegnati ai clienti.

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `tipo_lavoro` | string | Es. "Trasporto", "Smaltimento" |
| `customer_id` | FK → customers | |
| `status_lavoro` | string | Default `In Sospeso` |
| `data_esecuzione` | datetime nullable | |
| `costo_lavoro` | decimal(10,2) nullable | |
| `modalita_pagamento` | string nullable | |
| `nome_partenza` | string nullable | |
| `indirizzo_partenza` | string nullable | |
| `latitude_partenza` | decimal(10,7) nullable | |
| `longitude_partenza` | decimal(10,7) nullable | |
| `materiale` | string nullable | Nome materiale trasportato |
| `codice_eer` | string nullable | Codice EER rifiuto |
| `nome_destinazione` | string | |
| `indirizzo_destinazione` | string | |
| `latitude_destinazione` | decimal(10,7) nullable | |
| `longitude_destinazione` | decimal(10,7) nullable | |

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
Work         belongsTo Customer
Work         belongsToMany Workers     (work_worker)
Work         hasMany   Ricevute
Worker       belongsToMany Works       (work_worker)
Worker       belongsToMany Vehicles    (vehicle_worker)
Worker       belongsToMany CreditCards (credit_card_worker, no data_restituzione)
Worker       hasMany   CashMovements
Material     belongsToMany Deposits    (deposit_material)
CashMovement belongsTo Worker
CashMovement belongsTo Work (nullable)
CashMovement belongsTo CreditCard (nullable)
```
