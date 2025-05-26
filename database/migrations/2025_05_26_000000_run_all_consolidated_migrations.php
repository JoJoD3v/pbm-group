<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eseguiamo le migrazioni consolidate nell'ordine corretto
        $this->call('2025_05_26_000000_consolidated_users_table');
        $this->call('2025_05_26_000001_consolidated_materials_table');
        $this->call('2025_05_26_000002_consolidated_deposits_table');
        $this->call('2025_05_26_000006_consolidated_customers_table');
        $this->call('2025_05_26_000007_consolidated_warehouses_table');
        $this->call('2025_05_26_000003_consolidated_works_table');
        $this->call('2025_05_26_000004_consolidated_workers_table');
        $this->call('2025_05_26_000005_consolidated_vehicles_table');        $this->call('2025_05_26_000008_consolidated_credit_cards_table');
        $this->call('2025_05_26_000010_consolidated_pivot_tables');
        
        // Dopo aver creato tutte le tabelle, eseguiamo il seeder per creare l'utente sviluppatore
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\UserSeeder']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non facciamo nulla nel rollback, le singole migrazioni hanno già i propri metodi down
    }

    /**
     * Metodo per chiamare una migrazione specifica
     */
    protected function call($migration)
    {
        $file = database_path('migrations/' . $migration . '.php');
        if (file_exists($file)) {
            require_once $file;
            $instance = new class extends Migration {};
            $class = get_class($instance);
            (new $class)->up();
        }
    }
};
