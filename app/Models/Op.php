<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Op extends Model
{
    protected $fillable = [
        'patient_id',
        'patient_code',
        'op_reg_no',
        'company_id',
        'status',
        'op_status',
        'location',
        'payment_ref',
        'payment_type',
        'payment_amount',
        'tests_json',
        'tests_new_json',
        'packages_json',
        'grand_total',
    ];

    protected $casts = [
        'tests_json' => 'array',
        'packages_json' => 'array',
        'tests_new_json' => 'array',
    ];

    protected $appends = [
        'created_at_format',
    ];

    /* ------------------------------
        RELATIONSHIPS
    ------------------------------ */

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /* ------------------------------
        ACCESSORS
    ------------------------------ */

    public function getCreatedAtFormatAttribute()
    {
        return $this->created_at
            ? $this->created_at->format('d-m-Y')
            : null;
    }
}
