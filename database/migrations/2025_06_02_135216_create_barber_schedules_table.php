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
        Schema::create('barber_schedules', function (Blueprint $table) {
            $table->id(); // vẫn là unsigned, không sao vì nó không có ràng buộc khóa ngoại

            $table->bigInteger('barber_id'); // KHÔNG dùng unsignedBigInteger

            $table->date('date');
            $table->time('time_slot');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['barber_id', 'date', 'time_slot'], 'unique_barber_schedule');

            $table->foreign('barber_id')
                ->references('id')
                ->on('barbers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barber_schedules');
    }
};