<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outpatient_admissions', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 50)->unique();
            $table->foreignId('registration_id')->constrained('outpatient_registrations')->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->unsignedInteger('polyclinic_id');
            $table->unsignedInteger('doctor_id')->nullable();
            $table->unsignedInteger('guarantor_id')->nullable();
            $table->dateTime('admission_time');
            $table->dateTime('discharge_time')->nullable();
            $table->date('service_date');
            $table->string('entry_mode', 50)->nullable();
            $table->enum('status', ['waiting', 'admitted', 'in_service', 'completed', 'cancelled', 'deleted'])->default('waiting');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('polyclinic_id')
                ->references('id')
                ->on('polyclinics')
                ->restrictOnDelete();

            $table->foreign('doctor_id')
                ->references('id')
                ->on('medical_personnel')
                ->nullOnDelete();

            $table->foreign('guarantor_id')
                ->references('id')
                ->on('guarantors')
                ->nullOnDelete();

            $table->unique(['registration_id', 'polyclinic_id', 'service_date'], 'outpatient_admissions_reg_poly_date_unique');
            $table->index('admission_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outpatient_admissions');
    }
};
