<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'medical_record_number',
        'nik',
        'full_name',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'phone',
        'email',
        'religion',
        'blood_type',
        'marital_status',
        'default_guarantor_id',
        'ihs_id',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function defaultGuarantor(): BelongsTo
    {
        return $this->belongsTo(Guarantor::class, 'default_guarantor_id');
    }

    public function outpatientRegistrations(): HasMany
    {
        return $this->hasMany(OutpatientRegistration::class);
    }

    public function outpatientAdmissions(): HasMany
    {
        return $this->hasMany(OutpatientAdmission::class);
    }
}
