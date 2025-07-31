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
       Schema::table('orders', function (Blueprint $table) {
        $table->text('cancellation_reason')->nullable()->after('status');
        $table->string('cancellation_type')->nullable()->after('cancellation_reason'); // 'user' hoặc 'admin'
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
