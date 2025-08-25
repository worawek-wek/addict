<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // use HasFactory;
     protected $fillable = [
        'ref_branch_id',
        'order_number',
        'ref_customer_id', // <-- แก้ไขตรงนี้
        'ref_user_id',
        'ref_seller_id',
        'ref_room_id',
        'service_laundry_cost',
        'ref_status_id',
        'booking_date',
        'start_time',
        'end_time',
        'payment_method',
        'total_price',
        'duration_minutes',
    ];
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'orders';

    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'ref_status_id');
    }
    public function room()
    {
        return $this->belongsTo(Room::class, 'ref_room_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'ref_user_id');
    }
    public function addons()
    {
        return $this->hasMany(OrderHasAddonOption::class, 'ref_order_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'ref_customer_id');
    }
    public function products()
    {
        return $this->hasMany(OrderHasProduct::class, 'ref_order_id');
    }
}
