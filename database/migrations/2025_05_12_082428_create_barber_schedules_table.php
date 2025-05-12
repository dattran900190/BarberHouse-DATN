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
        Schema::create('barber_schedules', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('barber_id')->nullable();
            $table->date('date')->nullable();
            $table->time('time_slot')->nullable();
            $table->boolean('is_available')->nullable();

            $table->unique(['barber_id', 'date', 'time_slot'], 'barber_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barber_schedules');
    }
};
