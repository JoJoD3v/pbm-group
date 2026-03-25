<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->text('note')->nullable()->after('email');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->text('note')->nullable()->after('eer_code');
        });

        Schema::table('works', function (Blueprint $table) {
            $table->text('note')->nullable()->after('longitude_destinazione');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('note');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('note');
        });

        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
