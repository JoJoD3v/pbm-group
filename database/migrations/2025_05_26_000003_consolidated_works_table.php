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
        Schema::dropIfExists('works');
        
        // Creiamo la tabella con tutti i campi consolidati
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_lavoro');
            $table->unsignedBigInteger('customer_id');
            $table->string('status_lavoro')->default('In Sospeso');
            $table->date('data_esecuzione')->nullable();
            $table->decimal('costo_lavoro', 10, 2)->nullable();
            $table->string('modalita_pagamento')->nullable();
            $table->string('nome_partenza')->nullable();
            $table->string('indirizzo_partenza')->nullable();
            $table->decimal('latitude_partenza', 10, 7)->nullable();
            $table->decimal('longitude_partenza', 10, 7)->nullable();
            $table->string('materiale')->nullable();
            $table->string('codice_eer')->nullable();
            $table->string('nome_destinazione');
            $table->string('indirizzo_destinazione');
            $table->decimal('latitude_destinazione', 10, 7)->nullable();
            $table->decimal('longitude_destinazione', 10, 7)->nullable();
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
