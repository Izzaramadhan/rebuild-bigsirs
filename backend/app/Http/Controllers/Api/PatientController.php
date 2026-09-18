<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\IndexPatientRequest;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    public function __construct(
        private readonly PatientService $patientService
    ) {}

    /**
     * Display a listing of the patients.
     */
    public function index(IndexPatientRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $perPage = (int) ($validated['per_page'] ?? 15);
        $filters = ['q' => $validated['q'] ?? null];

        $patients = $this->patientService->listPatients($filters, $perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar pasien berhasil diambil.',
            'data' => PatientResource::collection($patients)->response()->getData(true),
        ]);
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = $this->patientService->createPatient($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data pasien berhasil disimpan.',
            'data' => new PatientResource($patient),
        ], 201);
    }

    /**
     * Display the specified patient.
     */
    public function show(Patient $patient): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail pasien berhasil diambil.',
            'data' => new PatientResource($patient),
        ]);
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        $patient = $this->patientService->updatePatient($patient, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data pasien berhasil diperbarui.',
            'data' => new PatientResource($patient),
        ]);
    }
}
