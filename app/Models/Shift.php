<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'start_time' => 'datetime:H:i',  // Store and display as HH:MM
        'end_time' => 'datetime:H:i',    // Store and display as HH:MM
    ];
}
