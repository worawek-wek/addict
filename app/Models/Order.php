<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    // use HasFactory;
    public const ORDER_NUMBER_COURSE = 'M';
    public const ORDER_NUMBER_PRODUCT = 'P';

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
        'paid_at',
        'sales_commission',
    ];
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'orders';

    public static function generateOrderNumber(string $suffix, $date = null): string
    {
        $date = $date ? Carbon::parse($date) : now();
        $suffix = strtoupper($suffix);

        return $date->format('dmY')
            . str_pad(static::nextOrderSequenceForYear($date), 7, '0', STR_PAD_LEFT)
            . $suffix;
    }

    public static function createWithGeneratedOrderNumber(array $attributes, string $suffix): self
    {
        return DB::transaction(function () use ($attributes, $suffix) {
            $attributes['order_number'] = static::generateOrderNumber($suffix);

            return static::create($attributes);
        });
    }

    protected static function nextOrderSequenceForYear(Carbon $date): int
    {
        $yearStart = $date->copy()->startOfYear();
        $yearEnd = $date->copy()->endOfYear();

        $countBasedSequence = static::query()
            ->where(function ($query) use ($yearStart, $yearEnd) {
                $query->whereBetween('created_at', [$yearStart, $yearEnd])
                    ->orWhere(function ($query) use ($yearStart, $yearEnd) {
                        $query->whereNull('created_at')
                            ->whereBetween('booking_date', [
                                $yearStart->toDateString(),
                                $yearEnd->toDateString(),
                            ]);
                    });
            })
            ->lockForUpdate()
            ->count() + 1;

        $year = $date->format('Y');
        $maxFormattedSequence = static::query()
            ->where('order_number', 'like', '____' . $year . '_______%')
            ->lockForUpdate()
            ->pluck('order_number')
            ->reduce(function ($max, $orderNumber) use ($year) {
                if (preg_match('/^\d{4}' . preg_quote($year, '/') . '(\d{7})[MP]$/', $orderNumber, $matches)) {
                    return max($max, (int) $matches[1]);
                }

                return $max;
            }, 0);

        return max($countBasedSequence, $maxFormattedSequence + 1);
    }

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
        return $this->belongsTo(User::class, 'ref_user_id')->withTrashed();
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'ref_seller_id')->withTrashed();
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
