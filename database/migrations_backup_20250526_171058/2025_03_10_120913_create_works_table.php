<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_lavoro');
            // Relazione col cliente registrato
            $table->unsignedBigInteger('customer_id');
            // Per il materiale: memorizziamo il nome (che potrà essere quello di un materiale registrato o un testo libero)
            $table->string('materiale')->nullable();
            // Codice EER (auto-riempito se si sceglie un materiale registrato)
            $table->string('codice_eer')->nullable();
            // Informazioni sulla destinazione
            $table->string('nome_destinazione'); // etichetta per la modalità scelta (ad es. "indirizzo cliente", "cantiere", "libero")
            $table->string('indirizzo_destinazione');
            $table->decimal('latitude_destinazione', 10, 7)->nullable();
            $table->decimal('longitude_destinazione', 10, 7)->nullable();
            $table->timestamps();
    
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('works');
    }
    
};
