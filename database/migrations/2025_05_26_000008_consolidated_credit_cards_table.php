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
        Schema::dropIfExists('credit_cards');
        
        // Creiamo la tabella con tutti i campi consolidati
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->text('numero_carta');
            $table->date('scadenza_carta');
            $table->decimal('fondo_carta', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
