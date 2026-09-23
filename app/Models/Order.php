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
    private const BUSINESS_DAY_START_HOUR = 10;
    private const BUSINESS_DAY_START_MINUTE = 1;

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
        $businessDate = static::resolveBusinessDate($date);

        return $businessDate->format('dmY')
            . '/'
            . static::nextOrderSequenceForBusinessDay($date)
            . '-'
            . $suffix;
    }

    public static function createWithGeneratedOrderNumber(array $attributes, string $suffix): self
    {
        return DB::transaction(function () use ($attributes, $suffix) {
            $attributes['order_number'] = static::generateOrderNumber($suffix);

            return static::create($attributes);
        });
    }

    /** รายการชำระเงินของบิลนี้ (จ่ายแยกหลายวิธีได้) */
    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'ref_order_id');
    }

    /**
     * บันทึกการชำระเงินของบิลนี้ (ล้างของเดิมแล้วเขียนใหม่ทั้งชุด)
     *  - $payments = [['method' => 'cash', 'amount' => 500], ['method' => 'qr_code', 'amount' => 300], ...]
     *  - นับเฉพาะแถวที่มีวิธี + จำนวนเงิน > 0
     *  - อัปเดต orders.payment_method เป็นสรุป: 1 วิธี = วิธีนั้น, หลายวิธี = 'split'
     *  - คืนจำนวนแถวที่บันทึกจริง
     */
    public function syncPayments(array $payments): int
    {
        $rows = [];
        foreach ($payments as $p) {
            $method = OrderPayment::normalizeMethod($p['method'] ?? '');
            $amount = round((float) ($p['amount'] ?? 0), 2);
            if ($method === '' || $amount <= 0) {
                continue;
            }
            $rows[] = ['method' => $method, 'amount' => $amount];
        }

        $this->payments()->delete();
        foreach ($rows as $r) {
            $this->payments()->create($r);
        }

        if (count($rows) === 1) {
            $this->payment_method = $rows[0]['method'];
        } elseif (count($rows) > 1) {
            $this->payment_method = 'split';
        }
        $this->save();

        return count($rows);
    }

    /**
     * แปลง request เป็น array รายการชำระเงิน — รองรับทั้งแบบใหม่ (จ่ายแยก) และเก่า (วิธีเดียว)
     *  - แบบใหม่: payments[] = [{method, amount}, ...]
     *  - แบบเก่า: payment_method (วิธีเดียว) -> ใช้ยอด $fallbackAmount ทั้งก้อน
     */
    public static function parsePaymentsFromRequest($request, float $fallbackAmount): array
    {
        $raw = $request->input('payments');
        if (is_array($raw) && count($raw) > 0) {
            return collect($raw)
                ->map(fn ($p) => [
                    'method' => $p['method'] ?? ($p['payment_method'] ?? ''),
                    'amount' => (float) preg_replace('/[^0-9.]/', '', (string) ($p['amount'] ?? 0)),
                ])
                ->filter(fn ($p) => $p['method'] !== '' && $p['amount'] > 0)
                ->values()
                ->all();
        }

        $method = $request->input('payment_method');
        if ($method) {
            return [['method' => $method, 'amount' => $fallbackAmount]];
        }

        return [];
    }

    protected static function nextOrderSequenceForBusinessDay(Carbon $date): int
    {
        [$businessDate, $windowStart, $windowEnd] = static::resolveBusinessDayWindow($date);
        $prefix = $businessDate->format('dmY') . '/';

        $countBasedSequence = static::query()
            ->where(function ($query) use ($windowStart, $windowEnd, $businessDate) {
                $query->whereBetween('created_at', [$windowStart, $windowEnd])
                    ->orWhere(function ($query) use ($businessDate) {
                        $query->whereNull('created_at')
                            ->whereDate('booking_date', $businessDate->toDateString());
                    });
            })
            ->lockForUpdate()
            ->count() + 1;

        $maxFormattedSequence = static::query()
            ->where('order_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->pluck('order_number')
            ->reduce(function ($max, $orderNumber) use ($prefix) {
                if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)-[A-Z]+$/', $orderNumber, $matches)) {
                    return max($max, (int) $matches[1]);
                }

                return $max;
            }, 0);

        return max($countBasedSequence, $maxFormattedSequence + 1);
    }

    protected static function resolveBusinessDate(Carbon $date): Carbon
    {
        [$businessDate] = static::resolveBusinessDayWindow($date);

        return $businessDate;
    }

    protected static function resolveBusinessDayWindow(Carbon $date): array
    {
        $businessDate = $date->copy();
        $businessDayStart = $date->copy()->setTime(self::BUSINESS_DAY_START_HOUR, self::BUSINESS_DAY_START_MINUTE, 0);

        if ($date->lt($businessDayStart)) {
            $businessDate->subDay();
        }

        $windowStart = $businessDate->copy()->setTime(self::BUSINESS_DAY_START_HOUR, self::BUSINESS_DAY_START_MINUTE, 0);
        $windowEnd = $windowStart->copy()->addDay()->subSecond();

        return [$businessDate->startOfDay(), $windowStart, $windowEnd];
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
