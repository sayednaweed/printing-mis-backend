<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    protected $guarded = [];

    public function icons()
    {
        return $this->hasMany(ExpenseTypeIcon::class);
    }

    public function translations()
    {
        return $this->hasMany(ExpenseTypeTran::class);
    }
}
