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
        $this->assertTrue(Schema::hasColumns('patients', ['id', 'medical_record_number', 'nik', 'full_name', 'birth_date', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('outpatient_registrations', ['id', 'patient_id', 'registration_date', 'channel', 'status', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('outpatient_admissions', ['id', 'admission_no', 'registration_id', 'patient_id', 'polyclinic_id', 'doctor_id', 'guarantor_id', 'service_date', 'admission_time', 'status', 'deleted_at']));
        $this->assertTrue(Schema::hasColumns('outpatient_queues', ['id', 'admission_id', 'polyclinic_id', 'queue_number', 'queue_date', 'status']));
    }

    public function test_patient_factory_creates_synthetic_patient_without_default_guarantor(): void
    {
        $patient = Patient::factory()->create([
            'birth_date' => '1990-01-15',
        ]);
        $registration = OutpatientRegistration::factory()->create(['patient_id' => $patient->id]);

        $this->assertDatabaseHas('patients', ['id' => $patient->id]);
        $this->assertInstanceOf(Carbon::class, $patient->birth_date);
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

    public function test_registration_belongs_to_patient(): void
    {
        $patient = Patient::factory()->create();
        $registration = OutpatientRegistration::factory()->create([
            'patient_id' => $patient->id,
            'status' => RegistrationStatus::Registered->value,
        ]);

        $this->assertEquals($patient->id, $registration->patient->id);
        $this->assertInstanceOf(RegistrationStatus::class, $registration->status);
    }

    public function test_registration_has_many_admissions(): void
    {
        $registration = OutpatientRegistration::factory()->create();
        $admission1 = OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'polyclinic_id' => Polyclinic::factory()->create()->id,
        ]);
        $admission2 = OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'polyclinic_id' => Polyclinic::factory()->create()->id,
        ]);

        $this->assertCount(2, $registration->admissions);
        $this->assertTrue($registration->admissions->contains($admission1));
        $this->assertTrue($registration->admissions->contains($admission2));
    }

    public function test_admission_belongs_to_registration_and_guarantor(): void
    {
        $registration = OutpatientRegistration::factory()->create();
        $guarantor = Guarantor::factory()->create();
        $admission = OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'guarantor_id' => $guarantor->id,
            'status' => AdmissionStatus::Waiting->value,
        ]);

        $this->assertEquals($registration->id, $admission->registration->id);
        $this->assertEquals($guarantor->id, $admission->guarantor->id);
        $this->assertInstanceOf(AdmissionStatus::class, $admission->status);
    }

    public function test_one_registration_can_have_two_admissions_on_different_polyclinics(): void
    {
        $registration = OutpatientRegistration::factory()->create();
        OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'polyclinic_id' => Polyclinic::factory()->create()->id,
            'service_date' => '2026-09-18',
        ]);
        OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'polyclinic_id' => Polyclinic::factory()->create()->id,
            'service_date' => '2026-09-18',
        ]);

        $this->assertEquals(2, $registration->admissions()->count());
    }

    public function test_one_registration_can_have_two_admissions_on_different_dates(): void
    {
        $registration = OutpatientRegistration::factory()->create();
        $polyclinic = Polyclinic::factory()->create();
        OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'polyclinic_id' => $polyclinic->id,
            'service_date' => '2026-09-18',
        ]);
        OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'polyclinic_id' => $polyclinic->id,
            'service_date' => '2026-09-19',
        ]);

        $this->assertEquals(2, $registration->admissions()->count());
    }

    public function test_admission_is_rejected_if_registration_polyclinic_and_service_date_are_same(): void
    {
        // Aturan lintas pendaftaran diuji pada tingkat service. Tingkat DB menguji pendaftaran yang sama.
        $registration = OutpatientRegistration::factory()->create();
        $polyclinic = Polyclinic::factory()->create();
        OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'polyclinic_id' => $polyclinic->id,
            'service_date' => '2026-09-18',
        ]);

        $this->expectException(QueryException::class);
        OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'polyclinic_id' => $polyclinic->id,
            'service_date' => '2026-09-18',
        ]);
    }

    public function test_admission_no_is_unique(): void
    {
        OutpatientAdmission::factory()->create(['admission_no' => 'RJ-20260918-0001']);

        $this->expectException(QueryException::class);
        OutpatientAdmission::factory()->create(['admission_no' => 'RJ-20260918-0001']);
    }

    public function test_admission_no_date_part_matches_service_date(): void
    {
        $admission = OutpatientAdmission::factory()->create();

        $serviceDateFormatted = Carbon::parse($admission->service_date)->format('Ymd');
        $this->assertStringStartsWith('RJ-'.$serviceDateFormatted, $admission->admission_no);
    }

    public function test_registration_id_is_required_for_admission(): void
    {
        $this->expectException(QueryException::class);
        OutpatientAdmission::factory()->create(['registration_id' => null]);
    }

    public function test_service_date_is_required_for_admission(): void
    {
        $this->expectException(QueryException::class);
        OutpatientAdmission::factory()->create(['service_date' => null]);
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
        $admission = OutpatientAdmission::factory()->create();
        $queue = OutpatientQueue::factory()->create([
            'admission_id' => $admission->id,
            'polyclinic_id' => $admission->polyclinic_id,
        ]);

        $this->assertEquals($admission->id, $queue->admission->id);
        $this->assertEquals($admission->polyclinic_id, $queue->polyclinic->id);
    }

    public function test_delete_behavior_matches_mapping(): void
    {
        $patient = Patient::factory()->create();
        $registration = OutpatientRegistration::factory()->create(['patient_id' => $patient->id]);
        $guarantor = Guarantor::factory()->create();
        $admission = OutpatientAdmission::factory()->create([
            'registration_id' => $registration->id,
            'patient_id' => $patient->id,
            'guarantor_id' => $guarantor->id,
        ]);

        $guarantor->delete();
        $admission->refresh();
        $this->assertNull($admission->guarantor_id);

        $registration->delete();
        $this->assertSoftDeleted('outpatient_registrations', ['id' => $registration->id]);

        $admission->delete();
        $this->assertSoftDeleted('outpatient_admissions', ['id' => $admission->id]);
    }
}
