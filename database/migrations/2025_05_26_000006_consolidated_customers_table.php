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
        Schema::dropIfExists('customers');
        
        // Creiamo la tabella con tutti i campi consolidati
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            // Campo per indicare se è "fisica" o "giuridica"
            $table->enum('customer_type', ['fisica', 'giuridica']);
    
            // Campi per Persona Fisica
            $table->string('full_name')->nullable();      // Nome e Cognome
            $table->string('codice_fiscale')->nullable();   // Codice Fiscale
    
            // Campi per Persona Giuridica
            $table->string('ragione_sociale')->nullable();  // Ragione Sociale
            $table->string('partita_iva')->nullable();        // Partita Iva
    
            // Campi comuni a entrambe le tipologie
            $table->string('address');     // Indirizzo (oppure "indirizzo_sede" per la giuridica)
            $table->string('phone');       // Numero di telefono
            $table->string('email');       // Email
            $table->decimal('latitude_customer', 10, 7)->nullable(); // Latitudine indirizzo
            $table->decimal('longitude_customer', 10, 7)->nullable(); // Longitudine indirizzo
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
