<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('medical_record_number', 50)->unique();
            $table->string('nik', 20)->nullable()->unique();
            $table->string('full_name', 100);
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('ihs_id', 100)->nullable();
            $table->enum('status', ['active', 'deleted'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('full_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
