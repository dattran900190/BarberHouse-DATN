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
            $table->unique(['barber_id', 'schedule_date', 'status', 'note'], 'unique_barber_schedule');
        });
    }

    public function down()
    {
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->dropUnique('unique_barber_schedule');
        });
    }
};
