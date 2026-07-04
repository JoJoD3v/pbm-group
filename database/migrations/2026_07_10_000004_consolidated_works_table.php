<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_lavoro');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('appaltatore_id')->nullable();
            $table->string('status_lavoro')->default('In Sospeso');
            $table->dateTime('data_esecuzione')->nullable();
            $table->decimal('costo_lavoro', 10, 2)->nullable();
            $table->string('modalita_pagamento')->nullable();
            $table->string('nome_partenza')->nullable();
            $table->string('indirizzo_partenza')->nullable();
            $table->decimal('latitude_partenza', 10, 7)->nullable();
            $table->decimal('longitude_partenza', 10, 7)->nullable();
            $table->string('materiale')->nullable();
            $table->string('codice_eer')->nullable();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->decimal('prezzo_materiale', 10, 2)->default(1.00);
            $table->decimal('quantita_materiale', 10, 2)->default(1.00);
            $table->boolean('iva_applicata')->default(false);
            $table->string('nome_destinazione')->nullable();
            $table->string('indirizzo_destinazione')->nullable();
            $table->unsignedBigInteger('deposit_id')->nullable();
            $table->unsignedBigInteger('warehouse_destinazione_id')->nullable();
            $table->decimal('latitude_destinazione', 10, 7)->nullable();
            $table->decimal('longitude_destinazione', 10, 7)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('appaltatore_id')->references('id')->on('appaltatori')->onDelete('set null');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('set null');
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('set null');
            $table->foreign('warehouse_destinazione_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
