<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * รายการชำระเงินต่อ 1 บิล (จ่ายแยกหลายวิธีได้) — 1 order มีได้หลายแถว
 */
class OrderPayment extends Model
{
    protected $table = 'order_payments';

    protected $fillable = [
        'ref_order_id',
        'method',
        'amount',
    ];

    public $timestamps = true;

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /** วิธีชำระมาตรฐาน (รวม promptpay/transfer -> qr_code) */
    public static function normalizeMethod($method): string
    {
        $m = strtolower(trim((string) $method));

        return [
            'promptpay' => 'qr_code',
            'transfer'  => 'qr_code',
            'qr'        => 'qr_code',
        ][$m] ?? $m;
    }

    /** ป้ายภาษาไทยของวิธีชำระ */
    public static function label($method): string
    {
        return [
            'cash'        => 'เงินสด',
            'qr_code'     => 'โอน/สแกน QR',
            'credit_card' => 'บัตรเครดิต/เดบิต',
            'alipay'      => 'Alipay',
            'wechat'      => 'WeChat Pay',
            'ewallet'     => 'E-Wallet',
            'split'       => 'จ่ายแยก',
        ][self::normalizeMethod($method)] ?? (string) $method;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'ref_order_id');
    }
}
