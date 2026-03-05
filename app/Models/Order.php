<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // use HasFactory;
     protected $fillable = [
        'type',
        'ref_branch_id',
        'order_number',
        'ref_customer_id',
        'ref_account_id',
        'ref_user_id',
        'ref_seller_id',
        'customer_type',
        'ref_room_id',
        'ref_room_type_id',
        'service_laundry_cost',
        'ref_status_id',
        'booking_date',
        'start_time',
        'end_time',
        'price',
        'discount',
        'total_price',
        'duration_minutes',
        'payment_method',
        'payment_status',
        'sales_commission',
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
    public function room_type()
    {
        return $this->belongsTo(RoomType::class, 'ref_room_type_id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'service_laundry_cost');
    }
    public function room()
    {
        return $this->belongsTo(Room::class, 'ref_room_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'ref_user_id');
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'ref_seller_id');
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
    public function drinks()
    {
        return $this->hasMany(OrderHasDrink::class, 'ref_order_id');
    }
    public function user_commission()
    {
        return $this->hasOne(UserHasRoomTypeCommission::class, 'ref_user_id', 'ref_user_id')
            ->where('ref_room_type_id', $this->ref_room_type_id)
            ->where('ref_course_id', $this->service_laundry_cost);
    }
    public function room_type_course()
    {
        return $this->hasOneThrough(
            RoomTypeHasCourse::class,
            RoomType::class,
            'id',
            'ref_room_type_id',
            'ref_room_type_id',
            'id'
        )->where('ref_course_id', $this->service_laundry_cost);
    }
}
