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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('order_id')->nullable()->index('order_id');
            $table->enum('method', ['momo', 'cash'])->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->enum('status', ['pending', 'paid' , 'failed', 'refunded'])->nullable();
            $table->string('transaction_code', 100)->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
