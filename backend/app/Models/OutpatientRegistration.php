<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Database\Factories\OutpatientRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutpatientRegistration extends Model
{
    /** @use HasFactory<OutpatientRegistrationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'registration_no',
        'patient_id',
        'registration_date',
        'guarantor_id',
        'bpjs_number',
        'channel',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'datetime',
            'status' => RegistrationStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function guarantor(): BelongsTo
    {
        return $this->belongsTo(Guarantor::class);
    }

    public function admission(): HasOne
    {
        return $this->hasOne(OutpatientAdmission::class, 'registration_id');
    }
}
