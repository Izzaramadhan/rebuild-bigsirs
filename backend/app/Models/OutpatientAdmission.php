<?php

namespace App\Models;

use App\Enums\AdmissionStatus;
use Database\Factories\OutpatientAdmissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutpatientAdmission extends Model
{
    /** @use HasFactory<OutpatientAdmissionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admission_no',
        'registration_id',
        'patient_id',
        'polyclinic_id',
        'doctor_id',
        'guarantor_id',
        'admission_time',
        'discharge_time',
        'service_date',
        'entry_mode',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'admission_time' => 'datetime',
            'discharge_time' => 'datetime',
            'service_date' => 'date',
            'status' => AdmissionStatus::class,
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(OutpatientRegistration::class, 'registration_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function polyclinic(): BelongsTo
    {
        return $this->belongsTo(Polyclinic::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(MedicalPersonnel::class, 'doctor_id');
    }

    public function guarantor(): BelongsTo
    {
        return $this->belongsTo(Guarantor::class);
    }

    public function queues(): HasMany
    {
        return $this->hasMany(OutpatientQueue::class, 'admission_id');
    }
}
