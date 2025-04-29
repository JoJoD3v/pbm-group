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
        Schema::table('vehicle_worker', function (Blueprint $table) {
            $table->dateTime('data_assegnazione')->change();
            $table->dateTime('data_restituzione')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_worker', function (Blueprint $table) {
            $table->date('data_assegnazione')->change();
            $table->date('data_restituzione')->nullable()->change();
        });
    }
};
