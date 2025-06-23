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
            $table->enum('status', ['off', 'custom'])->default('custom')->after('schedule_date');
        });
    }

    public function down(): void
    {
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
