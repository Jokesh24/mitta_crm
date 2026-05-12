<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'mobile_number',
        'patient_name',
        'dob',
        'age',
        'gender',
        'added_by_role',
        'added_by',
    ];

    public function ops()
    {
        return $this->hasMany(Op::class);
    }
}
