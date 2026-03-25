<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('n_aut_comunicazione')->nullable()->after('address');
            $table->string('numero_iscrizione_albo')->nullable()->after('n_aut_comunicazione');
            $table->string('tipo')->nullable()->after('numero_iscrizione_albo');
            $table->string('destinazione')->nullable()->after('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['n_aut_comunicazione', 'numero_iscrizione_albo', 'tipo', 'destinazione']);
        });
    }
};
