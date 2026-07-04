# Migrazioni Consolidate

Secondo consolidamento (2026-07-10) delle migration del progetto PBM Group / T.E.P. srl. Sostituisce il consolidamento precedente (`2025_05_26_*`) più tutte le migration incrementali (`add_*`, `fix_*`, `update_*`) accumulate da allora.

## Migrazioni disponibili

Un file per tabella/gruppo logico, in ordine di esecuzione:

1. `2026_07_10_000001_consolidated_users_table.php` — users, password_reset_tokens, sessions
2. `2026_07_10_000002_consolidated_materials_table.php` — materials
3. `2026_07_10_000003_consolidated_deposits_table.php` — deposits
4. `2026_07_10_000004_consolidated_works_table.php` — works
5. `2026_07_10_000005_consolidated_workers_table.php` — workers
6. `2026_07_10_000006_consolidated_vehicles_table.php` — vehicles
7. `2026_07_10_000007_consolidated_customers_table.php` — customers
8. `2026_07_10_000008_consolidated_warehouses_table.php` — warehouses
9. `2026_07_10_000009_consolidated_credit_cards_table.php` — credit_cards
10. `2026_07_10_000010_consolidated_services_table.php` — services
11. `2026_07_10_000011_consolidated_appaltatori_table.php` — appaltatori
12. `2026_07_10_000012_consolidated_bordero_tables.php` — pezzi_bordero, bordero, bordero_pezzi
13. `2026_07_10_000013_consolidated_work_servizi_table.php` — work_servizi
14. `2026_07_10_000014_consolidated_worker_mansioni_table.php` — worker_mansioni (con backfill `trasportatore` sui worker esistenti)
15. `2026_07_10_000015_consolidated_pivot_tables.php` — deposit_material, work_worker, vehicle_worker, credit_card_worker
16. `2026_07_10_000016_consolidated_cash_movements_table.php` — cash_movements
17. `2026_07_10_000017_consolidated_ricevute_table.php` — ricevute
18. `2026_07_10_000018_consolidated_vehicle_assignment_logs_table.php` — vehicle_assignment_logs
19. `2026_07_10_000019_consolidated_credit_card_recharges_table.php` — credit_card_recharges

## Utilizzo

Ambiente nuovo (dev locale o setup da zero):

```bash
php artisan migrate
```

oppure, per un DB già popolato ma senza tabelle, uno scaffolding pulito:

```bash
php artisan migrate:fresh
```

**Attenzione**: `migrate:fresh` cancella tutte le tabelle esistenti. Non eseguirlo su un database di produzione con dati reali.

## Import manuale MySQL

Per creare lo schema completo senza passare da Artisan (es. import diretto in un server MySQL di produzione), usare:

```
database/migrations/consolidated_full_schema.mysql.sql
```

Contiene tutti i `CREATE TABLE` nell'ordine corretto (rispetto alle foreign key).

## Nota su ambienti già migrati

Le migration `2025_05_26_*` e le successive `add_*`/`fix_*`/`update_*` sono state rimosse da questa cartella perché sostituite dai file `2026_07_10_*` sopra elencati. Su un ambiente dove quelle vecchie migration erano già state eseguite (quindi lo schema è già corretto), **non serve rieseguire nulla** — i nuovi file sono utili solo per ambienti nuovi o per ricostruire lo schema da zero. Se in un ambiente esistente Artisan segnala migration mancanti nella tabella `migrations`, verificare prima che lo schema reale corrisponda a quello descritto in `docs/DATABASE.md` prima di eseguire qualunque comando distruttivo.
