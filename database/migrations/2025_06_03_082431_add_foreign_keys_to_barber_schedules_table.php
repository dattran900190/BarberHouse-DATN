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
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->foreign(['barber_id'], 'barber_schedules_ibfk_1')->references(['id'])->on('barbers')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->dropForeign('barber_schedules_ibfk_1');
        });
    }
};
