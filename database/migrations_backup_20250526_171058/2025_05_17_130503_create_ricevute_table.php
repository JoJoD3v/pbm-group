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
        Schema::create('ricevute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')->constrained()->onDelete('cascade');
            $table->string('numero_ricevuta')->unique();
            $table->boolean('fattura')->default(false);
            $table->boolean('riserva_controlli')->default(false);
            $table->string('nome_ricevente');
            $table->text('firma_base64')->nullable();
            $table->boolean('pagamento_effettuato')->default(false);
            $table->decimal('somma_pagamento', 10, 2)->nullable();
            $table->string('foto_bolla')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ricevute');
    }
};
