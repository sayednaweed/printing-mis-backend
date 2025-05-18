<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationConfiguration extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationConfigurationFactory> */
    use HasFactory;
    protected $guarded = [];
}
