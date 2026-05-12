<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    /**
     * Table name (optional if it matches plural)
     */
    protected $table = 'branches';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'branch_name',
        'location_id',
        'status',
    ];

    /**
     * Branch belongs to a Location
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}
