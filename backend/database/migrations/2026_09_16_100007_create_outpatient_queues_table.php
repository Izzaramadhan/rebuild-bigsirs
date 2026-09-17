<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outpatient_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->nullable()->constrained('outpatient_admissions')->nullOnDelete();
            $table->unsignedInteger('polyclinic_id');
            $table->string('queue_number', 20);
            $table->date('queue_date');
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->foreign('polyclinic_id')
                ->references('id')
                ->on('polyclinics')
                ->restrictOnDelete();

            $table->index(['polyclinic_id', 'queue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outpatient_queues');
    }
};
