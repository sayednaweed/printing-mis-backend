<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentStatusFactory> */
    use HasFactory;
    protected $guarded = [];

    public function vaccinePayment()
    {
        $this->hasMany(VaccinePayment::class, 'payment_status_id', 'id');
    }

    public function paymentStatusTran()
    {
        $this->hasMany(PaymentStatusTran::class, 'payment_status_id', 'id');
    }
}
