<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pezzi_bordero', function (Blueprint $table) {
            $table->id();
            $table->string('nome_pezzo')->unique();
            $table->timestamps();
        });

        Schema::create('bordero', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_id')->unique();
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->string('status')->default('In Sospeso');
            $table->text('note_tecniche')->nullable();
            $table->timestamps();

            $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('set null');
        });

        Schema::create('bordero_pezzi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bordero_id');
            $table->unsignedBigInteger('pezzo_bordero_id')->nullable();
            $table->string('nome_pezzo');
            $table->integer('quantita')->default(1);
            $table->timestamps();

            $table->foreign('bordero_id')->references('id')->on('bordero')->onDelete('cascade');
            $table->foreign('pezzo_bordero_id')->references('id')->on('pezzi_bordero')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bordero_pezzi');
        Schema::dropIfExists('bordero');
        Schema::dropIfExists('pezzi_bordero');
    }
};
