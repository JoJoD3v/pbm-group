<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // Fix nullable constraint on columns that may have been created as NOT NULL
            $table->string('n_aut_comunicazione')->nullable()->change();
            $table->string('numero_iscrizione_albo')->nullable()->change();
            $table->string('tipo')->nullable()->change();
            $table->string('destinazione')->nullable()->change();

            // New field
            $table->date('data_scadenza')->nullable()->after('destinazione');
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('data_scadenza');
        });
    }
};
