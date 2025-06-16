<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseTypeIcon extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseTypeIconFactory> */
    use HasFactory;
    protected $guarded = [];

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
