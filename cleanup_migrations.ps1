# Script PowerShell per eliminare i vecchi file di migrazione
# Da eseguire dalla directory principale del progetto

# Elenco dei file di migrazione consolidati da mantenere
$keepFiles = @(
    "2025_05_26_000000_run_all_consolidated_migrations.php",
    "2025_05_26_000000_consolidated_users_table.php",
    "2025_05_26_000001_consolidated_materials_table.php",
    "2025_05_26_000002_consolidated_deposits_table.php",
    "2025_05_26_000003_consolidated_works_table.php",
    "2025_05_26_000004_consolidated_workers_table.php",
    "2025_05_26_000005_consolidated_vehicles_table.php",
    "2025_05_26_000006_consolidated_customers_table.php",
    "2025_05_26_000007_consolidated_warehouses_table.php",
    "2025_05_26_000008_consolidated_credit_cards_table.php",
    "2025_05_26_000010_consolidated_pivot_tables.php",
    "README_MIGRATION_CONSOLIDATE.md"
)

# Directory delle migrazioni
$migrationsDir = ".\database\migrations"

# Ottieni tutti i file nella directory delle migrazioni che non sono nella lista dei file da conservare
$filesToDelete = Get-ChildItem -Path $migrationsDir -File | Where-Object { $keepFiles -notcontains $_.Name }

# Creazione di un backup dei file che verranno eliminati
$backupDir = ".\database\migrations_backup_" + (Get-Date -Format "yyyyMMdd_HHmmss")
New-Item -ItemType Directory -Path $backupDir -Force
Write-Host "Creazione backup nella cartella: $backupDir"

foreach ($file in $filesToDelete) {
    Copy-Item -Path $file.FullName -Destination $backupDir
    Write-Host "Backup del file: $($file.Name)"
}

# Eliminazione dei vecchi file di migrazione
foreach ($file in $filesToDelete) {
    Remove-Item -Path $file.FullName -Force
    Write-Host "Eliminato file: $($file.Name)"
}

Write-Host ""
Write-Host "Operazione completata!"
Write-Host "Sono stati eliminati $($filesToDelete.Count) file di migrazione."
Write-Host "È stato creato un backup di tutti i file eliminati nella directory: $backupDir"
Write-Host ""
Write-Host "Ricorda di eseguire le migrazioni consolidate con il comando:"
Write-Host "php artisan migrate --path=database/migrations/2025_05_26_000000_run_all_consolidated_migrations.php"
