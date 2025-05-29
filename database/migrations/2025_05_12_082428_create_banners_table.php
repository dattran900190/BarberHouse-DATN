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
        Schema::create('banners', function (Blueprint $table) {
            $table->id(); // id (primary key, auto increment)
            $table->string('title', 255);
            $table->string('image_url', 255);
            $table->string('link_url', 255)->nullable(); // có thể không có link
            $table->boolean('is_active')->default(true); // true: hiển thị, false: ẩn
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
