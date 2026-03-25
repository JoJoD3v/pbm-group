<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->unsignedBigInteger('deposit_id')->nullable()->after('nome_destinazione');
            $table->unsignedBigInteger('warehouse_destinazione_id')->nullable()->after('deposit_id');

            $table->foreign('deposit_id')->references('id')->on('deposits')->nullOnDelete();
            $table->foreign('warehouse_destinazione_id')->references('id')->on('warehouses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropForeign(['deposit_id']);
            $table->dropForeign(['warehouse_destinazione_id']);
            $table->dropColumn(['deposit_id', 'warehouse_destinazione_id']);
        });
    }
};
