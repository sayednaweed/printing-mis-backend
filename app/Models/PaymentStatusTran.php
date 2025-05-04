<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatusTran extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentStatusTranFactory> */
    use HasFactory;
    protected $guarded = [];

    public function paymentStatus()
    {
        $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }
}
