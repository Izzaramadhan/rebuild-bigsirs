<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_personnel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('str_number', 50)->nullable()->unique();
            $table->string('sip_number', 50)->nullable();
            $table->string('dpjp_code', 50)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_personnel');
    }
};
