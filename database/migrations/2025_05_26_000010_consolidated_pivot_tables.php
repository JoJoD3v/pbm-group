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
        // Eliminiamo le vecchie tabelle se esistono
        Schema::dropIfExists('deposit_material');
        Schema::dropIfExists('work_worker');
        Schema::dropIfExists('vehicle_worker');
        Schema::dropIfExists('credit_card_worker');
        
        // Creiamo la tabella pivot deposit_material
        Schema::create('deposit_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
        
        // Creiamo la tabella pivot work_worker
        Schema::create('work_worker', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_id');
            $table->unsignedBigInteger('worker_id');
            $table->timestamps();
            
            $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
        });
        
        // Creiamo la tabella pivot vehicle_worker
        Schema::create('vehicle_worker', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('worker_id');
            $table->datetime('data_assegnazione');
            $table->datetime('data_restituzione')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
        });
        
        // Creiamo la tabella pivot credit_card_worker
        Schema::create('credit_card_worker', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_card_id');
            $table->unsignedBigInteger('worker_id');
            $table->timestamps();
            
            $table->foreign('credit_card_id')->references('id')->on('credit_cards')->onDelete('cascade');
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_material');
        Schema::dropIfExists('work_worker');
        Schema::dropIfExists('vehicle_worker');
        Schema::dropIfExists('credit_card_worker');
    }
};
