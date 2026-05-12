<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'team';

    protected $fillable = [
        'first_name',
        'last_name',
        'emp_code',
        'email',
        'mobile',
        'role',
        'password',
        'branch_id',
        'date_of_join',
        'status',
        'address'
    ];

    /** A Team Member belongs to a Branch */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
