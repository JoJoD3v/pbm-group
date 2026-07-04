<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appaltatori', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_soggetto', ['fisica', 'giuridica']);
            $table->string('full_name')->nullable();
            $table->string('codice_fiscale')->nullable();
            $table->string('ragione_sociale')->nullable();
            $table->string('partita_iva')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('latitude_appaltatore', 10, 7)->nullable();
            $table->decimal('longitude_appaltatore', 10, 7)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appaltatori');
    }
};
