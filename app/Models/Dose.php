<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dose extends Model
{
    //

    protected $guarded = [];

    
    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'vaccine_id');
    }

    public function user(){
        return $this->belongsTo(EpiUser::class,'epi_user_id');
    }
}
