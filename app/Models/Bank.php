<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    // use HasFactory;
    protected $fillable = [
        'bank_name',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'banks';
}
