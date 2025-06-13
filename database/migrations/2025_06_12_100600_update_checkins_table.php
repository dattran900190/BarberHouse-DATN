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
    Schema::table('checkins', function (Blueprint $table) {
        $table->unsignedBigInteger('appointment_id')->after('id');
        $table->string('qr_code_value')->after('appointment_id');
        $table->boolean('is_checked_in')->default(false)->after('qr_code_value');
        $table->timestamp('checkin_time')->nullable()->after('is_checked_in');
    });
}

public function down()
{
    Schema::table('checkins', function (Blueprint $table) {
        $table->dropColumn(['appointment_id', 'qr_code_value', 'is_checked_in', 'checkin_time']);
    });
}

};
