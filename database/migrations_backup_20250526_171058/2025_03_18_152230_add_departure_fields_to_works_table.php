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
            $table->string('nome_partenza')->nullable()->after('customer_id');
            $table->string('indirizzo_partenza')->nullable()->after('nome_partenza');
            $table->decimal('latitude_partenza', 10, 7)->nullable()->after('indirizzo_partenza');
            $table->decimal('longitude_partenza', 10, 7)->nullable()->after('latitude_partenza');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn('nome_partenza');
            $table->dropColumn('indirizzo_partenza');
            $table->dropColumn('latitude_partenza');
            $table->dropColumn('longitude_partenza');
        });
    }
};
