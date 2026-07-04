<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_mansioni', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->enum('mansione', ['trasportatore', 'posatore']);
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->unique(['worker_id', 'mansione']);
        });

        $now = now();
        $workerIds = \DB::table('workers')->pluck('id');
        foreach ($workerIds as $workerId) {
            \DB::table('worker_mansioni')->insert([
                'worker_id' => $workerId,
                'mansione' => 'trasportatore',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_mansioni');
    }
};
