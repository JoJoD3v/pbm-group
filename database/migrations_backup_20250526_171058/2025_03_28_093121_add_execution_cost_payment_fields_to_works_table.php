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
        Schema::table('works', function (Blueprint $table) {
            $table->date('data_esecuzione')->nullable()->after('status_lavoro');
            $table->decimal('costo_lavoro', 10, 2)->nullable()->after('data_esecuzione');
            $table->string('modalita_pagamento')->nullable()->after('costo_lavoro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn('data_esecuzione');
            $table->dropColumn('costo_lavoro');
            $table->dropColumn('modalita_pagamento');
        });
    }
};
