<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'status',
        'last_login'
    ];

    protected $hidden = [
        'password'
    ];
}
