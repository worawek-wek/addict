<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    // use HasFactory;
    protected $fillable = [
        'position_name',
        'is_uniform_commission',
        'commission_type',
        'commission_value',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'positions';
}
