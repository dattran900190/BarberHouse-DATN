<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->string('status', 20)->default('custom')->after('schedule_date');
        });
    }

    public function down(): void
    {
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
