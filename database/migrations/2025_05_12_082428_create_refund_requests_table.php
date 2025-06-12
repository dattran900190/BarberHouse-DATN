<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id')->index();
            $table->text('reason');
            $table->bigInteger('appointment_id')->nullable()->index();
            $table->decimal('refund_amount', 10, 2);
            $table->string('bank_account_name', 100);
            $table->string('bank_account_number', 50);
            $table->string('bank_name', 100);
            $table->enum('refund_status', ['pending', 'refunded'])->default('pending');
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
