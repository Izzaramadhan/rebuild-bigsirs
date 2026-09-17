<?php

namespace App\Models;

use Database\Factories\MedicalPersonnelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalPersonnel extends Model
{
    /** @use HasFactory<MedicalPersonnelFactory> */
    use HasFactory;

    protected $table = 'medical_personnel';

    protected $fillable = [
        'name',
        'str_number',
        'sip_number',
        'dpjp_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function outpatientAdmissions(): HasMany
    {
        return $this->hasMany(OutpatientAdmission::class, 'doctor_id');
    }
}
