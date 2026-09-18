<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Database\Factories\OutpatientRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutpatientRegistration extends Model
{
    /** @use HasFactory<OutpatientRegistrationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'registration_date',
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

    public function admissions(): HasMany
    {
        return $this->hasMany(OutpatientAdmission::class, 'registration_id');
    }
}
