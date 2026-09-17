<?php

namespace App\Models;

use Database\Factories\PolyclinicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Polyclinic extends Model
{
    /** @use HasFactory<PolyclinicFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'bpjs_code',
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
        return $this->hasMany(OutpatientAdmission::class);
    }

    public function outpatientQueues(): HasMany
    {
        return $this->hasMany(OutpatientQueue::class);
    }
}
