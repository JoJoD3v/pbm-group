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
        Schema::table('credit_card_recharges', function (Blueprint $table) {
            $table->dateTime('data_ricarica')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_card_recharges', function (Blueprint $table) {
            $table->date('data_ricarica')->change();
        });
    }
};
