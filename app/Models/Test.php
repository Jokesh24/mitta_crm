<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $table = 'tests';

    protected $fillable = [
        'test_name',
        'unit_id',
        'normal_range',
        'display_order',
        'price',
        'status',
    ];

    // Relation with Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function children()
    {
        return $this->hasMany(Test::class, 'parent_id')->orderBy('display_order');
    }

    public function parent()
    {
        return $this->belongsTo(Test::class, 'parent_id');
    }


}
