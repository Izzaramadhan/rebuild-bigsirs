<?php

namespace App\Models;

use Database\Factories\OutpatientQueueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutpatientQueue extends Model
{
    /** @use HasFactory<OutpatientQueueFactory> */
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'polyclinic_id',
        'queue_number',
        'queue_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'status' => 'integer',
        ];
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(OutpatientAdmission::class, 'admission_id');
    }

    public function polyclinic(): BelongsTo
    {
        return $this->belongsTo(Polyclinic::class);
    }
}
