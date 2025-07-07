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
        Schema::create('cancelled_appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('barber_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('service_id');
            $table->dateTime('appointment_time');
            $table->string('status')->default('cancelled');
            $table->string('payment_status')->nullable();
            $table->text('note')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('cancellation_type')->nullable(); // Thêm trường này: 'cancelled' hoặc 'no-show'
            $table->string('status_before_cancellation')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->timestamps();
            $table->index(['barber_id', 'branch_id', 'appointment_time'], 'cancelled_appt_barber_branch_time_idx');
        });

        // Di chuyển các bản ghi cancelled hiện có
        $CancelledAppointments = \App\Models\Appointment::where('status', 'cancelled')->get();
        foreach ($CancelledAppointments as $appointment) {
            \App\Models\CancelledAppointment::create($appointment->toArray());
            $appointment->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_appointments');
    }
};
