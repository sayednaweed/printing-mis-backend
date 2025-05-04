<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\template\Auditable;
use Sway\Traits\InvalidatableToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class FinanceUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\FinanceUserFactory> */
    use InvalidatableToken, HasFactory, Notifiable, Auditable;
    protected $guarded = [];
}
