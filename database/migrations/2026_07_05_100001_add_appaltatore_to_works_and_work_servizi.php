<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->unsignedBigInteger('appaltatore_id')->nullable()->after('customer_id');
            $table->foreign('appaltatore_id')->references('id')->on('appaltatori')->onDelete('set null');

            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->string('nome_destinazione')->nullable()->change();
            $table->string('indirizzo_destinazione')->nullable()->change();
        });

        Schema::create('work_servizi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('nome_servizio');
            $table->decimal('prezzo_unitario', 10, 2);
            $table->integer('quantita')->default(1);
            $table->boolean('iva_applicata')->default(false);
            $table->timestamps();

            $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_servizi');

        Schema::table('works', function (Blueprint $table) {
            $table->dropForeign(['appaltatore_id']);
            $table->dropColumn('appaltatore_id');

            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->string('nome_destinazione')->nullable(false)->change();
            $table->string('indirizzo_destinazione')->nullable(false)->change();
        });
    }
};
