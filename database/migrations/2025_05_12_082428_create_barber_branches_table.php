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
        Schema::create('barber_branches', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger ('barber_id');
            $table->bigInteger('branch_id');
            $table->timestamp('assigned_at')->useCurrent();
            $table->boolean('is_active')->default(true);

            $table->unique(['barber_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barber_branches');
    }
};
