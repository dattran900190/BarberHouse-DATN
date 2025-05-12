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
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign(['user_id'], 'reviews_ibfk_1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['barber_id'], 'reviews_ibfk_2')->references(['id'])->on('barbers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['appointment_id'], 'reviews_ibfk_3')->references(['id'])->on('appointments')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign('reviews_ibfk_1');
            $table->dropForeign('reviews_ibfk_2');
            $table->dropForeign('reviews_ibfk_3');
        });
    }
};
