<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionsHistory extends Model
{
    protected $table = 'commissions_history';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'user_message_id',
        'user_sales_id',
        'order_id',
        'commission_massage_amount',
    ];

    // Relationships
    public function massageStaff()
    {
        return $this->belongsTo(User::class, 'user_message_id');
    }
    public function salesStaff()
    {
        return $this->belongsTo(User::class, 'user_sales_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
