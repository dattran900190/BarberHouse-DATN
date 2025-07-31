<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index();
            $table->text('reason');
            $table->bigInteger('order_id')->nullable()->index();
            $table->bigInteger('appointment_id')->nullable()->index();
            $table->decimal('refund_amount', 10, 2);
            $table->string('bank_account_name', 100);
            $table->string('bank_account_number', 50);
            $table->string('bank_name', 100);
            $table->enum('refund_status', ['pending', 'processing', 'refunded', 'rejected'])->default('pending');
            $table->text('reject_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });

        // Thêm CHECK CONSTRAINTs bằng SQL thuần
        DB::statement("ALTER TABLE refund_requests ADD CONSTRAINT single_refundable_entity CHECK (order_id IS NULL OR appointment_id IS NULL)");
        DB::statement("ALTER TABLE refund_requests ADD CONSTRAINT require_refundable_entity CHECK (order_id IS NOT NULL OR appointment_id IS NOT NULL)");
    }

    public function down(): void
    {
        // Xoá constraint trước khi xoá bảng (tùy vào CSDL, có thể cần hoặc không)
        DB::statement("ALTER TABLE refund_requests DROP CONSTRAINT IF EXISTS single_refundable_entity");
        DB::statement("ALTER TABLE refund_requests DROP CONSTRAINT IF EXISTS require_refundable_entity");

        Schema::dropIfExists('refund_requests');
    }
};
