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
        Schema::table('user_redeemed_vouchers', function (Blueprint $table) {
            $table->foreign(['user_id'], 'user_redeemed_vouchers_user_id_fk')
                  ->references(['id'])->on('users')
                  ->onUpdate('no action')
                  ->onDelete('no action');

            $table->foreign(['promotion_id'], 'user_redeemed_vouchers_promotion_id_fk')
                  ->references(['id'])->on('promotions')
                  ->onUpdate('no action')
                  ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_redeemed_vouchers', function (Blueprint $table) {
            $table->dropForeign('user_redeemed_vouchers_user_id_fk');
            $table->dropForeign('user_redeemed_vouchers_promotion_id_fk');
        });
    }
};
