# Migrazioni Consolidate

Questo pacchetto di migrazioni è stato creato per facilitare la migrazione del database del progetto PBM Group a un nuovo ambiente.

## Creazione automatica dell'utente sviluppatore

Durante la migrazione, verrà creato automaticamente un utente con i seguenti dati:
- **Nome**: JoJo
- **Email**: giovannicastaldodev@gmail.com
- **Password**: K1t4mmu0rt!
- **Ruolo**: Sviluppatore

Questi dati sono configurati nel seeder `UserSeeder` che viene chiamato automaticamente durante l'esecuzione della migrazione principale.

## Migrazioni Disponibili

Le migrazioni sono state consolidate nei seguenti file:

1. `2025_05_26_000000_consolidated_users_table.php` - Tabella utenti con tutti i campi
2. `2025_05_26_000001_consolidated_materials_table.php` - Tabella materiali
3. `2025_05_26_000002_consolidated_deposits_table.php` - Tabella depositi
4. `2025_05_26_000003_consolidated_works_table.php` - Tabella lavori
5. `2025_05_26_000004_consolidated_workers_table.php` - Tabella lavoratori
6. `2025_05_26_000005_consolidated_vehicles_table.php` - Tabella veicoli
7. `2025_05_26_000006_consolidated_customers_table.php` - Tabella clienti
8. `2025_05_26_000007_consolidated_warehouses_table.php` - Tabella cantieri
9. `2025_05_26_000008_consolidated_credit_cards_table.php` - Tabella carte prepagate
10. `2025_05_26_000010_consolidated_pivot_tables.php` - Tabelle pivot (relazioni many-to-many)

## Come utilizzare le migrazioni consolidate

### Opzione 1: Eseguire tutte le migrazioni in una volta

Puoi eseguire tutte le migrazioni consolidate con un solo comando utilizzando il file master:

```bash
php artisan migrate --path=database/migrations/2025_05_26_000000_run_all_consolidated_migrations.php
```

### Opzione 2: Eseguire le migrazioni singolarmente

Puoi anche eseguire ogni migrazione singolarmente se preferisci avere più controllo:

```bash
php artisan migrate --path=database/migrations/2025_05_26_000000_consolidated_users_table.php
php artisan migrate --path=database/migrations/2025_05_26_000001_consolidated_materials_table.php
# ... e così via per gli altri file
```

## Pulizia delle vecchie migrazioni

Per rimuovere i vecchi file di migrazione e mantenere solo quelli consolidati, è stato predisposto uno script PowerShell:

```powershell
.\cleanup_migrations.ps1
```

Questo script:
1. Crea un backup di tutti i file di migrazione vecchi in una cartella `database/migrations_backup_YYYYMMDD_HHMMSS`
2. Rimuove tutti i vecchi file di migrazione lasciando solo quelli consolidati
3. Fornisce un riepilogo dell'operazione

## Nota importante

Prima di eseguire queste migrazioni consolidate, assicurati di:

1. Aver fatto un backup completo del database
2. Non avere migrazioni in sospeso nel sistema attuale

Per un ambiente completamente nuovo, queste migrazioni creeranno la struttura del database senza necessità di eseguire le vecchie migrazioni incrementali.

## Risoluzione problemi

Se incontri problemi di vincoli di chiave estera durante la migrazione, prova a modificare l'ordine di esecuzione delle migrazioni nel file `2025_05_26_000000_run_all_consolidated_migrations.php`.
