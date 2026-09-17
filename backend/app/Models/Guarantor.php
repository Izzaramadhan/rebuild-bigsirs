<?php

namespace App\Models;

use App\Enums\GuarantorType;
use Database\Factories\GuarantorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guarantor extends Model
{
    /** @use HasFactory<GuarantorFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => GuarantorType::class,
            'is_active' => 'boolean',
        ];
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'default_guarantor_id');
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
