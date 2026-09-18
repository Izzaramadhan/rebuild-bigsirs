<?php

namespace Tests\Feature\Api;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_is_unauthorized_to_access_patient_endpoints(): void
    {
        $this->getJson('/api/patients')->assertStatus(401);
        $this->getJson('/api/patients/1')->assertStatus(401);
        $this->postJson('/api/patients', [])->assertStatus(401);
        $this->patchJson('/api/patients/1', [])->assertStatus(401);
    }

    public function test_authenticated_user_can_list_patients_with_pagination(): void
    {
        Patient::factory()->count(20)->create();

        $response = $this->actingAs($this->user)->getJson('/api/patients');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data',
                    'links',
                    'meta',
                ],
            ]);

        $this->assertCount(15, $response->json('data.data'));
    }

    public function test_user_can_search_patients(): void
    {
        Patient::factory()->create(['full_name' => 'Budi Santoso', 'nik' => '1234567890']);
        Patient::factory()->create(['full_name' => 'Andi Wijaya', 'nik' => '0987654321']);

        $response = $this->actingAs($this->user)->getJson('/api/patients?q=Budi');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('Budi Santoso', $response->json('data.data.0.full_name'));
    }

    public function test_soft_deleted_patients_are_not_listed(): void
    {
        $patient = Patient::factory()->create();
        $patient->delete();

        $response = $this->actingAs($this->user)->getJson('/api/patients');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data.data'));
    }

    public function test_per_page_cannot_exceed_100(): void
    {
        Patient::factory()->count(1)->create();

        $response = $this->actingAs($this->user)->getJson('/api/patients?per_page=150');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_user_can_create_valid_patient_with_auto_generated_rm(): void
    {
        $data = [
            'full_name' => 'Pasien Baru',
            'nik' => '3333444455556666',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/patients', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'full_name' => 'Pasien Baru',
                    'medical_record_number' => '000001',
                ],
            ]);

        $this->assertDatabaseHas('patients', [
            'full_name' => 'Pasien Baru',
            'medical_record_number' => '000001',
        ]);
    }

    public function test_client_cannot_determine_medical_record_number(): void
    {
        $data = [
            'full_name' => 'Hacker',
            'medical_record_number' => '999999',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['medical_record_number']);
    }

    public function test_client_cannot_change_medical_record_number(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs($this->user)->patchJson('/api/patients/'.$patient->id, [
            'medical_record_number' => '999999',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['medical_record_number']);
    }

    public function test_client_cannot_change_status(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs($this->user)->patchJson('/api/patients/'.$patient->id, [
            'status' => 'deleted',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_medical_record_number_increases(): void
    {
        $this->actingAs($this->user)->postJson('/api/patients', ['full_name' => 'P1']);
        $this->actingAs($this->user)->postJson('/api/patients', ['full_name' => 'P2']);

        $this->assertDatabaseHas('patients', ['full_name' => 'P1', 'medical_record_number' => '000001']);
        $this->assertDatabaseHas('patients', ['full_name' => 'P2', 'medical_record_number' => '000002']);
    }

    public function test_legacy_rm_does_not_break_generator(): void
    {
        Patient::factory()->create(['medical_record_number' => 'RM-OLD-123']);

        $this->actingAs($this->user)->postJson('/api/patients', ['full_name' => 'Baru']);
        $this->assertDatabaseHas('patients', ['full_name' => 'Baru', 'medical_record_number' => '000001']);
    }

    public function test_soft_deleted_rm_is_not_reused(): void
    {
        $patient = $this->actingAs($this->user)->postJson('/api/patients', ['full_name' => 'Del'])->json('data');
        Patient::find($patient['id'])->delete();

        $response = $this->actingAs($this->user)->postJson('/api/patients', ['full_name' => 'New']);
        $response->assertJsonPath('data.medical_record_number', '000002');
    }

    public function test_duplicate_nik_is_rejected(): void
    {
        Patient::factory()->create(['nik' => '1111']);

        $response = $this->actingAs($this->user)->postJson('/api/patients', [
            'full_name' => 'Test',
            'nik' => '1111',
        ]);

        $response->assertStatus(422);
    }

    public function test_multiple_patients_can_have_null_nik(): void
    {
        $this->actingAs($this->user)->postJson('/api/patients', ['full_name' => 'Test1', 'nik' => null])->assertStatus(201);
        $this->actingAs($this->user)->postJson('/api/patients', ['full_name' => 'Test2', 'nik' => null])->assertStatus(201);
    }

    public function test_user_can_view_patient_details(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs($this->user)->getJson('/api/patients/'.$patient->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $patient->id);
    }

    public function test_soft_deleted_patient_returns_404(): void
    {
        $patient = Patient::factory()->create();
        $patient->delete();

        $this->actingAs($this->user)->getJson('/api/patients/'.$patient->id)->assertStatus(404);
    }

    public function test_user_can_update_patient(): void
    {
        $patient = Patient::factory()->create(['full_name' => 'Lama']);

        $response = $this->actingAs($this->user)->patchJson('/api/patients/'.$patient->id, [
            'full_name' => 'Baru',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Baru', $response->json('data.full_name'));
        $this->assertEquals($patient->medical_record_number, $response->json('data.medical_record_number'));
    }

    public function test_updating_own_nik_is_allowed(): void
    {
        $patient = Patient::factory()->create(['nik' => '1111222233334444']);

        $response = $this->actingAs($this->user)->patchJson('/api/patients/'.$patient->id, [
            'full_name' => 'Ubah NIK Sendiri',
            'nik' => '1111222233334444',
        ]);

        $response->assertStatus(200);
    }

    public function test_updating_to_someone_elses_nik_is_rejected(): void
    {
        Patient::factory()->create(['nik' => '1111222233334444']);
        $patient2 = Patient::factory()->create(['nik' => '5555666677778888']);

        $response = $this->actingAs($this->user)->patchJson('/api/patients/'.$patient2->id, [
            'full_name' => 'Ambil NIK',
            'nik' => '1111222233334444',
        ]);

        $response->assertStatus(422);
    }

    public function test_empty_string_is_normalized_to_null(): void
    {
        $patient = Patient::factory()->create(['nik' => '1111222233334444', 'address' => 'Jalan A']);

        $response = $this->actingAs($this->user)->patchJson('/api/patients/'.$patient->id, [
            'full_name' => 'Test',
            'nik' => '',
            'address' => '',
        ]);

        $response->assertStatus(200);
        $this->assertNull($response->json('data.nik'));
        $this->assertNull($response->json('data.address'));
    }

    public function test_update_requires_patch_and_disallows_put(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs($this->user)->patchJson('/api/patients/'.$patient->id, [
            'full_name' => 'Valid Patch',
        ])->assertStatus(200);

        $this->actingAs($this->user)->putJson('/api/patients/'.$patient->id, [
            'full_name' => 'Invalid Put',
        ])->assertStatus(405);
    }

    public function test_delete_produces_405(): void
    {
        $patient = Patient::factory()->create();
        $this->actingAs($this->user)->deleteJson('/api/patients/'.$patient->id)->assertStatus(405);
    }

    public function test_per_page_validation(): void
    {
        Patient::factory()->count(2)->create();

        $this->actingAs($this->user)->getJson('/api/patients?per_page=1')->assertStatus(200)->assertJsonPath('data.meta.per_page', 1);
        $this->actingAs($this->user)->getJson('/api/patients?per_page=100')->assertStatus(200)->assertJsonPath('data.meta.per_page', 100);

        $this->actingAs($this->user)->getJson('/api/patients?per_page=0')->assertStatus(422)->assertJsonValidationErrors(['per_page']);
        $this->actingAs($this->user)->getJson('/api/patients?per_page=101')->assertStatus(422)->assertJsonValidationErrors(['per_page']);
        $this->actingAs($this->user)->getJson('/api/patients?per_page=abc')->assertStatus(422)->assertJsonValidationErrors(['per_page']);
    }

    public function test_search_q_validation_max_length(): void
    {
        $q = str_repeat('a', 101);
        $this->actingAs($this->user)->getJson('/api/patients?q='.$q)->assertStatus(422)->assertJsonValidationErrors(['q']);
    }

    public function test_wildcard_search_is_escaped(): void
    {
        Patient::factory()->create(['full_name' => 'John Doe']);
        Patient::factory()->create(['full_name' => 'Percent%Name']);
        Patient::factory()->create(['full_name' => 'Underscore_Name']);

        $responseNormal = $this->actingAs($this->user)->getJson('/api/patients?q=John');
        $responseNormal->assertStatus(200);
        $this->assertCount(1, $responseNormal->json('data.data'));

        $responsePercent = $this->actingAs($this->user)->getJson('/api/patients?q=%');
        $responsePercent->assertStatus(200);
        $this->assertCount(1, $responsePercent->json('data.data'));
        $this->assertEquals('Percent%Name', $responsePercent->json('data.data.0.full_name'));

        $responseUnderscore = $this->actingAs($this->user)->getJson('/api/patients?q=_');
        $responseUnderscore->assertStatus(200);
        $this->assertCount(1, $responseUnderscore->json('data.data'));
        $this->assertEquals('Underscore_Name', $responseUnderscore->json('data.data.0.full_name'));
    }

    public function test_soft_deleted_patient_does_not_appear_in_search(): void
    {
        $patient = Patient::factory()->create(['full_name' => 'Deleted Patient']);
        $patient->delete();

        $response = $this->actingAs($this->user)->getJson('/api/patients?q=Deleted');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data.data'));
    }
}
