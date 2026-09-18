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
        Schema::table('outpatient_admissions', function (Blueprint $table) {
            $table->renameIndex('outpatient_admissions_registration_number_unique', 'outpatient_admissions_admission_no_unique');
            $table->renameColumn('registration_number', 'admission_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outpatient_admissions', function (Blueprint $table) {
            $table->renameColumn('admission_no', 'registration_number');
            $table->renameIndex('outpatient_admissions_admission_no_unique', 'outpatient_admissions_registration_number_unique');
        });
    }
};
