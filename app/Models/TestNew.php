<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestNew extends Model
{
    use HasFactory;

    protected $table = 'tests_new';

    protected $fillable = [
        'test_name',
        'parameters', // Stores JSON array of IDs from 'tests' table
        'price',
        'status',
    ];

    // Optional: Cast parameters to array automatically (makes using it easier)
    // However, since we store as JSON string in controller manually to match your package logic, 
    // we can skip casting or just handle it as string.
}