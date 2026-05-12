<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLoginLog extends Model
{
    protected $table = 'admin_login_logs';

    protected $fillable = [
        'admin_id',
        'ip_address',
        'city',
        'region',
        'country',
        'browser',
        'device',
        'logged_in_at'
    ];
}
