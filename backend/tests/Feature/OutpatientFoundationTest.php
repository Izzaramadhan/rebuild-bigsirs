<?php

namespace Tests\Feature;

use App\Enums\AdmissionStatus;
use App\Enums\GuarantorType;
use App\Enums\RegistrationStatus;
use App\Models\Guarantor;
use App\Models\MedicalPersonnel;
use App\Models\OutpatientAdmission;
use App\Models\OutpatientQueue;
use App\Models\OutpatientRegistration;
use App\Models\Patient;
use App\Models\Polyclinic;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OutpatientFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_tables_and_core_columns_exist(): void
    {
        $this->assertTrue(Schema::hasColumns('guarantors', ['id', 'code', 'name', 'type', 'is_active']));
        $this->assertTrue(Schema::hasColumns('polyclinics', ['id', 'code', 'name', 'type', 'bpjs_code', 'is_active']));
        $this->assertTrue(Schema::hasColumns('medical_personnel', ['id', 'name', 'str_number', 'sip_number', 'dpjp_code', 'is_active']));
        $this->assertTrue(Schema::hasColumns('patients', ['id', 'medical_record_number', 'nik', 'full_name', 'birth_date', 'default_guarantor_id', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('outpatient_registrations', ['id', 'registration_no', 'patient_id', 'guarantor_id', 'registration_date', 'channel', 'status', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('outpatient_admissions', ['id', 'admission_no', 'registration_id', 'patient_id', 'polyclinic_id', 'doctor_id', 'guarantor_id', 'admission_time', 'status', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('outpatient_queues', ['id', 'admission_id', 'polyclinic_id', 'queue_number', 'queue_date', 'status']));
    }

    public function test_patient_factory_creates_synthetic_patient_with_casts_and_relations(): void
    {
        $guarantor = Guarantor::factory()->create();
        $patient = Patient::factory()->create([
            'birth_date' => '1990-01-15',
            'default_guarantor_id' => $guarantor->id,
        ]);
        $registration = OutpatientRegistration::factory()->create(['patient_id' => $patient->id]);

        $this->assertDatabaseHas('patients', ['id' => $patient->id]);
        $this->assertInstanceOf(Carbon::class, $patient->birth_date);
        $this->assertEquals($guarantor->id, $patient->defaultGuarantor->id);
        $this->assertTrue($patient->outpatientRegistrations->contains($registration));
    }

    public function test_patient_business_identifiers_are_unique_when_provided(): void
    {
        Patient::factory()->create(['medical_record_number' => 'RM-UNIQUE-001', 'nik' => '1234567890123456']);

        $this->expectException(QueryException::class);
        Patient::factory()->create(['medical_record_number' => 'RM-UNIQUE-001', 'nik' => '6543210987654321']);
    }

    public function test_patient_nik_is_nullable_and_unique_when_provided(): void
    {
        Patient::factory()->create(['nik' => null]);
        Patient::factory()->create(['nik' => null]);
        Patient::factory()->create(['nik' => '1111222233334444']);

        $this->expectException(QueryException::class);
        Patient::factory()->create(['nik' => '1111222233334444']);
    }

    public function test_registration_belongs_to_patient_and_guarantor_and_casts_status(): void
    {
        $patient = Patient::factory()->create();
        $guarantor = Guarantor::factory()->create();
        $registration = OutpatientRegistration::factory()->create([
            'patient_id' => $patient->id,
            'guarantor_id' => $guarantor->id,
            'status' => RegistrationStatus::Registered->value,
        ]);

        $this->assertEquals($patient->id, $registration->patient->id);
        $this->assertEquals($guarantor->id, $registration->guarantor->id);
        $this->assertInstanceOf(RegistrationStatus::class, $registration->status);
    }

    public function test_registration_number_is_unique_and_has_one_admission(): void
    {
        $patient = Patient::factory()->create();
        $polyclinic = Polyclinic::factory()->create();
        $registration = OutpatientRegistration::factory()->create([
            'registration_no' => 'REG-UNIQUE-001',
            'patient_id' => $patient->id,
        ]);
        $admission = OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'patient_id' => $patient->id,
            'polyclinic_id' => $polyclinic->id,
        ]);

        $this->assertEquals($admission->id, $registration->admission->id);
        $this->expectException(QueryException::class);
        OutpatientRegistration::factory()->create(['registration_no' => 'REG-UNIQUE-001']);
    }

    public function test_admission_relations_optional_doctor_and_status_cast(): void
    {
        $patient = Patient::factory()->create();
        $polyclinic = Polyclinic::factory()->create();
        $guarantor = Guarantor::factory()->create();
        $admission = OutpatientAdmission::factory()->create([
            'patient_id' => $patient->id,
            'polyclinic_id' => $polyclinic->id,
            'doctor_id' => null,
            'guarantor_id' => $guarantor->id,
            'status' => AdmissionStatus::Waiting->value,
        ]);

        $this->assertEquals($polyclinic->id, $admission->polyclinic->id);
        $this->assertNull($admission->doctor);
        $this->assertEquals($guarantor->id, $admission->guarantor->id);
        $this->assertInstanceOf(AdmissionStatus::class, $admission->status);
    }

    public function test_admission_number_and_registration_link_are_unique(): void
    {
        $patient = Patient::factory()->create();
        $polyclinic = Polyclinic::factory()->create();
        $registration = OutpatientRegistration::factory()->create(['patient_id' => $patient->id]);
        OutpatientAdmission::factory()->create([
            'admission_no' => 'ADM-UNIQUE-001',
            'registration_id' => $registration->id,
            'patient_id' => $patient->id,
            'polyclinic_id' => $polyclinic->id,
        ]);

        $this->expectException(QueryException::class);
        OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'patient_id' => $patient->id,
            'polyclinic_id' => $polyclinic->id,
        ]);
    }

    public function test_master_factories_create_valid_synthetic_data_and_unique_codes(): void
    {
        $guarantor = Guarantor::factory()->create(['code' => 'G-UNIQUE-001']);
        $polyclinic = Polyclinic::factory()->create(['code' => 'POL-UNIQUE-001']);
        $personnel = MedicalPersonnel::factory()->create();

        $this->assertInstanceOf(GuarantorType::class, $guarantor->type);
        $this->assertTrue($polyclinic->is_active);
        $this->assertNotEmpty($personnel->name);

        $this->expectException(QueryException::class);
        Polyclinic::factory()->create(['code' => 'POL-UNIQUE-001']);
    }

    public function test_queue_belongs_to_admission_and_polyclinic(): void
    {
        $patient = Patient::factory()->create();
        $polyclinic = Polyclinic::factory()->create();
        $admission = OutpatientAdmission::factory()->create([
            'patient_id' => $patient->id,
            'polyclinic_id' => $polyclinic->id,
        ]);
        $queue = OutpatientQueue::factory()->create([
            'admission_id' => $admission->id,
            'polyclinic_id' => $polyclinic->id,
        ]);

        $this->assertEquals($admission->id, $queue->admission->id);
        $this->assertEquals($polyclinic->id, $queue->polyclinic->id);
    }

    public function test_delete_behavior_matches_mapping(): void
    {
        $guarantor = Guarantor::factory()->create();
        $patient = Patient::factory()->create(['default_guarantor_id' => $guarantor->id]);
        $registration = OutpatientRegistration::factory()->create(['patient_id' => $patient->id]);
        $polyclinic = Polyclinic::factory()->create();
        $admission = OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'patient_id' => $patient->id,
            'polyclinic_id' => $polyclinic->id,
        ]);

        $guarantor->delete();
        $patient->refresh();
        $this->assertNull($patient->default_guarantor_id);

        $registration->delete();
        $admission->refresh();
        $this->assertEquals($registration->id, $admission->registration_id);

        $admission->delete();
        $this->assertSoftDeleted('outpatient_admissions', ['id' => $admission->id]);
    }
}
