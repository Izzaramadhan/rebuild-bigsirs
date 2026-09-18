<?php

namespace App\Http\Requests\Patient;

use App\Http\Requests\ApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StorePatientRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'medical_record_number' => ['prohibited'],
            'status' => ['prohibited'],
            'deleted_at' => ['prohibited'],
            'created_at' => ['prohibited'],
            'updated_at' => ['prohibited'],
            'nik' => ['nullable', 'string', 'digits:16', 'unique:patients,nik'],
            'full_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'in:L,P'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'birth_place' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'religion' => ['nullable', 'string', 'max:30'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'ihs_id' => ['nullable', 'string', 'max:100'],
        ];
    }
}
