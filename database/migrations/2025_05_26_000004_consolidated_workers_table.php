<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminiamo la vecchia tabella se esiste
        Schema::dropIfExists('workers');
        
        // Creiamo la tabella con tutti i campi consolidati        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('id_worker')->unique();
            $table->string('name_worker');
            $table->string('cognome_worker');
            $table->string('license_worker');
            $table->string('worker_email')->unique();
            $table->string('phone_worker')->nullable();
            $table->decimal('fondo_cassa', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
