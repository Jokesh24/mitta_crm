<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultDetail extends Model
{
    protected $fillable = [
        'result_header_id',
        'test_id',
        'result_value',
        'is_abnormal',
        'unit_id',
        'normal_range',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
