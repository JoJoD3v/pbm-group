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
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('tipo_movimento', ['entrata', 'uscita']);
            $table->decimal('importo', 10, 2);
            $table->string('motivo')->nullable();
            $table->enum('metodo_pagamento', ['contanti', 'dkv', 'carta']);
            $table->foreignId('credit_card_id')->nullable()->constrained()->onDelete('set null');
            $table->date('data_movimento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
}; 