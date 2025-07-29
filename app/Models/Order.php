<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // use HasFactory;
    protected $fillable = [
        'order_number',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'orders';
    
    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }
}
