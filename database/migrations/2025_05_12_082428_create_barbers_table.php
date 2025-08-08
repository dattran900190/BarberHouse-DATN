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
        Schema::create('barbers', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('branch_id')->nullable()->index('branch_id');
            $table->string('name', 100)->nullable();
            $table->text('profile')->nullable();
             $table->enum('skill_level', ['assistant', 'junior', 'senior', 'master', 'expert'])->nullable();
            $table->string('avatar')->nullable();
            $table->float('rating_avg')->nullable();
            $table->enum('status', ['idle', 'retired'])->default('idle');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barbers');
    }
};
