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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id')->nullable()->index('user_id');
            $table->bigInteger('branch_id')->nullable()->index('branch_id');
            $table->decimal('total', 10)->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->nullable();
            $table->enum('payment_method', ['cash', 'momo', 'vnpay', 'card'])->nullable();
            $table->string('shipping_name', 100)->nullable();
            $table->string('shipping_phone', 20)->nullable();
            $table->text('shipping_address')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
