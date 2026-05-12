<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultHeader extends Model
{
    protected $fillable = [
        'patient_id',
        'op_id',
        'mobile_number',
        'whatsapp_sent',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function results()
    {
        return $this->hasMany(ResultDetail::class, 'result_header_id');
    }

    public function op()
    {
        return $this->belongsTo(Op::class, 'op_id');
    }
}
