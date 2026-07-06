<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chiusura_giorno_righe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chiusura_giorno_id')->constrained('chiusure_giorno')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained()->onDelete('cascade');
            $table->decimal('apertura_fondo_cassa', 10, 2)->default(0);
            $table->decimal('apertura_carta', 10, 2)->default(0);
            $table->decimal('chiusura_fondo_cassa', 10, 2)->default(0);
            $table->decimal('chiusura_carta', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chiusura_giorno_righe');
    }
};
