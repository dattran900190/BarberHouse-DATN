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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign(['user_id'], 'appointments_ibfk_1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['barber_id'], 'appointments_ibfk_2')->references(['id'])->on('barbers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['service_id'], 'appointments_ibfk_3')->references(['id'])->on('services')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['branch_id'], 'appointments_ibfk_4')->references(['id'])->on('branches')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['promotion_id'], 'appointments_ibfk_5')->references(['id'])->on('promotions')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign('appointments_ibfk_1');
            $table->dropForeign('appointments_ibfk_2');
            $table->dropForeign('appointments_ibfk_3');
            $table->dropForeign('appointments_ibfk_4');
            $table->dropForeign('appointments_ibfk_5');
        });
    }
};
