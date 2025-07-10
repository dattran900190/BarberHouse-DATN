<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->foreign('user_id', 'refund_requests_user_id_fk')
                ->references('id')->on('users')
                ->onUpdate('no action')
                ->onDelete('no action');

            $table->foreign('order_id', 'refund_requests_order_id_fk')
                ->references('id')->on('orders')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreign(['appointment_id'], 'refund_requests_ibfk_2')
                ->references(['id'])->on('appointments')
                ->onUpdate('no action')->onDelete('no action');
        });
    }

    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropForeign('refund_requests_user_id_fk');
            $table->dropForeign('refund_requests_order_id_fk');
            $table->dropForeign('refund_requests_ibfk2');
        });
    }
};
