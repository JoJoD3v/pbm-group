<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->enum('customer_type', ['fisica', 'giuridica']);
            $table->string('full_name')->nullable();
            $table->string('codice_fiscale')->nullable();
            $table->string('ragione_sociale')->nullable();
            $table->string('partita_iva')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->text('note')->nullable();
            $table->decimal('latitude_customer', 10, 7)->nullable();
            $table->decimal('longitude_customer', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
