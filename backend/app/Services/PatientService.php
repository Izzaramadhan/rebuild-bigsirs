<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PatientService
{
    public function __construct(
        private readonly MedicalRecordNumberGenerator $rmGenerator
    ) {}

    public function listPatients(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Patient::query();

        if (! empty($filters['q'])) {
            $searchTerm = $filters['q'];
            $escapedTerm = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $searchTerm);
            $query->where(function ($q) use ($escapedTerm) {
                $q->where('medical_record_number', 'like', "%{$escapedTerm}%")
                    ->orWhere('nik', 'like', "%{$escapedTerm}%")
                    ->orWhere('full_name', 'like', "%{$escapedTerm}%");
            });
        }

        // Only active patients by default because of soft deletes on Patient model
        return $query->paginate($perPage);
    }

    public function createPatient(array $data): Patient
    {
        return DB::transaction(function () use ($data) {
            $data['medical_record_number'] = $this->rmGenerator->generate();

            return Patient::create($data);
        });
    }

    public function updatePatient(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            // Ensure RM number cannot be updated
            unset($data['medical_record_number']);

            $patient->update($data);

            return $patient;
        });
    }
}
