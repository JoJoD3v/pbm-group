<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('n_aut_comunicazione')->nullable();
            $table->string('numero_iscrizione_albo')->nullable();
            $table->string('tipo')->nullable();
            $table->string('destinazione')->nullable();
            $table->string('piva')->nullable();
            $table->date('data_scadenza')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
