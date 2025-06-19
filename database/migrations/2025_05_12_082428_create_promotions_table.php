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
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('code', 50)->unique();               // Mã giảm giá, duy nhất
            $table->text('description')->nullable(); // Mô tả khuyến mãi, có thể null
            $table->integer('required_points')->nullable();
            $table->integer('usage_limit')->default(1);
            $table->enum('discount_type', ['fixed', 'percent']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->decimal('min_order_value', 10, 2)->nullable();
            $table->integer('quantity');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // created_at và updated_at                             
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
