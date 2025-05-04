<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    //
    protected $guarded = [];


    public function visit() {
            $this->belongsTo(Visit::class,'visit_id');
    }
    public function vaccineCenter() {
        $this->belongsTo(VaccineCenter::class,'vaccine_center_id');
    }
    public function epiUser()  {
        $this->belongsTo(EpiUser::class,'epi_user_id');
    }
    public function dose(){

        $this->hasMany(Dose::class,'vaccine_id');
        
    }
}
