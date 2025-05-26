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
        Schema::create('user_redeemed_vouchers', function (Blueprint $table) {
            $table->bigInteger('id', true); // BIGINT, PK, auto-increment
            $table->bigInteger('user_id')->index('user_id');
            $table->bigInteger('promotion_id')->index('promotion_id');
            $table->timestamp('redeemed_at');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();

            
            $table->unique(['user_id', 'promotion_id']); // Một user không thể đổi 1 voucher 2 lần

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_redeemed_vouchers');
    }
};
