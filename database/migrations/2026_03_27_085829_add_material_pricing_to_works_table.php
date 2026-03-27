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
            $table->unsignedBigInteger('material_id')->nullable()->after('codice_eer');
            $table->decimal('prezzo_materiale', 10, 2)->default(1.00)->after('material_id');
            $table->decimal('quantita_materiale', 10, 2)->default(1.00)->after('prezzo_materiale');
            $table->boolean('iva_applicata')->default(false)->after('quantita_materiale');

            $table->foreign('material_id')->references('id')->on('materials')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropColumn(['material_id', 'prezzo_materiale', 'quantita_materiale', 'iva_applicata']);
        });
    }
};
