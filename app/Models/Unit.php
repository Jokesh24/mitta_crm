<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'unit_name',
        'status',
    ];

    // Helper accessor (optional)
    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'Active' : 'Inactive';
    }
}
