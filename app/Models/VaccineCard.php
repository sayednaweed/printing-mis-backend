<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineCard extends Model
{
    //
    protected $guarded = [];

    public function vaccinePayment() {
        $this->belongsTo(VaccinePayment::class,'vaccine_payment_id');
    }

    public function receipt(){
        $this->hasOne(VaccineCard::class,'vaccine_payment_id','vaccine_payment_id');
    }
}
