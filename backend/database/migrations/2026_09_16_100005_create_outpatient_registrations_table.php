<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outpatient_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->dateTime('registration_date');
            $table->string('bpjs_number', 50)->nullable();
            $table->enum('channel', ['offline', 'online'])->default('offline');
            $table->enum('status', ['draft', 'registered', 'cancelled', 'deleted'])->default('registered');
            $table->timestamps();
            $table->softDeletes();

            $table->index('registration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outpatient_registrations');
    }
};
