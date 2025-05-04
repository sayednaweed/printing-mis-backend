<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccinePayment extends Model
{
    //
    protected $guarded = [];

    public function receipt()
    {
        return $this->hasOne(Reciept::class, 'vaccine_payment_id');
    }
}
