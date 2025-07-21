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
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('appointment_code')->unique();
            $table->bigInteger('user_id')->nullable()->index('user_id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('barber_id')->nullable();
            $table->bigInteger('branch_id')->nullable()->index('branch_id');
            $table->bigInteger('service_id')->nullable()->index('service_id');
            $table->json('additional_services')->nullable();
            $table->dateTime('appointment_time')->nullable();
            $table->enum('status', ['pending','unconfirmed', 'confirmed', 'checked-in', 'progress', 'completed', 'cancelled'])->nullable();
            $table->enum('payment_method', ['cash', 'vnpay'])->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'failed', 'refunded'])->nullable();
            $table->text('note')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('status_before_cancellation')->nullable();
            $table->bigInteger('promotion_id')->nullable()->index('promotion_id');
            $table->decimal('discount_amount', 10)->nullable()->default(0);
            $table->decimal('total_amount', 10, 2)->nullable()->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['barber_id', 'branch_id', 'appointment_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
