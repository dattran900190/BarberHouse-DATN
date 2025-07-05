<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->date('holiday_start_date')->nullable()->after('schedule_date');
            $table->date('holiday_end_date')->nullable()->after('holiday_start_date');
            $table->string('note')->nullable()->after('end_time');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->dropColumn(['holiday_start_date', 'holiday_end_date', 'note']);
        });
    }
};
