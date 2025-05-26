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
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigInteger('id',true); // ID tự tăng
            $table->bigInteger('order_id')->nullable()->index('order_id'); // Liên kết đơn hàng
            $table->bigInteger('product_variant_id')->nullable()->index('product_variant_id'); // Sản phẩm (biến thể)
            $table->integer('quantity')->nullable(); // Số lượng
            $table->decimal('price_at_time', 10, 2)->nullable(); // Giá tại thời điểm mua
            $table->decimal('total_price', 12, 2)->nullable(); // Tổng giá = quantity * price_at_time
            $table->timestamp('created_at')->nullable()->useCurrent(); // Ngày tạo
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate(); // Ngày cập nhật
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
