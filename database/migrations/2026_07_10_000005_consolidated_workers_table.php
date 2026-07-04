<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('id_worker')->unique();
            $table->string('name_worker');
            $table->string('cognome_worker');
            $table->string('license_worker');
            $table->string('worker_email')->unique();
            $table->string('phone_worker')->nullable();
            $table->decimal('fondo_cassa', 10, 2)->default(0.00);
            $table->string('colore_bg', 7)->nullable();
            $table->string('colore_font', 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
