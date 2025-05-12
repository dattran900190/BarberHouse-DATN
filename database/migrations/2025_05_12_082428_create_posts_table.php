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
        Schema::create('posts', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('title')->nullable();
            $table->string('slug')->nullable()->unique('slug');
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->bigInteger('author_id')->nullable()->index('author_id');
            $table->enum('status', ['draft', 'published', 'archived'])->nullable()->default('draft');
            $table->dateTime('published_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
