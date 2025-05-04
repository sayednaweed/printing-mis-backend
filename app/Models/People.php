<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    protected $guarded = [];


    public function visits()
    {
        return $this->hasMany(Visit::class, 'people_id');
    }
}
