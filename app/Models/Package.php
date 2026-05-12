<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'package_name',
        'tests',
        'parameters',
        'package_price',
        'status',
    ];

    protected $casts = [
        'tests' => 'array', // convert JSON to array automatically
        'parameters' => 'array', // convert JSON to array automatically
    ];
}
