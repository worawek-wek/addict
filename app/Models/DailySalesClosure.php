<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySalesClosure extends Model
{
    use HasFactory;
     protected $table = 'daily_sales_closures';

    protected $fillable = [
        'name',
    ];
}
