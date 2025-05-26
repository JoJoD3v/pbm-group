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
        Schema::dropIfExists('warehouses');
        
        // Creiamo la tabella con tutti i campi consolidati
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('nome_sede');
            $table->string('indirizzo');
            $table->decimal('latitude_warehouse', 10, 7)->nullable();
            $table->decimal('longitude_warehouse', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
