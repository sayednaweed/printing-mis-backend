<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    //
    protected $guarded = [];


    public function vaccine()
    {
        $this->hasMany(Vaccine::class, 'visit_id');
    }
    public function vaccinePayment()
    {
        $this->hasMany(VaccinePayment::class, 'vaccine_payment_id');
    }
}
