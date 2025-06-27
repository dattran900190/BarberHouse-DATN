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
            $table->bigInteger('id',true); // Tự động tăng ID
            $table->string('order_code')->unique();
            $table->bigInteger('user_id')->nullable()->index('user_id'); // ID người dùng
            $table->string('name'); // Tên người nhận
            $table->string('email')->nullable(); // Email người nhận
            $table->string('phone'); // Số điện thoại
            $table->text('address'); // Địa chỉ
            $table->string('shipping_method')->nullable(); // Phương thức vận chuyển
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total_money', 10, 2); // Tổng tiền
            $table->enum('status', ['pending', 'processing','shipping', 'completed', 'cancelled'])->nullable(); // Trạng thái đơn
            $table->enum('payment_method', ['cash', 'momo', 'vnpay', 'card'])->nullable(); // Phương thức thanh toán
            $table->enum('payment_status', ['unpaid', 'paid', 'failed', 'refunded'])->default('unpaid');
            $table->text('note')->nullable(); // Ghi chú
            $table->timestamp('created_at')->nullable()->useCurrent(); // Ngày tạo
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate(); // Ngày cập nhật
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