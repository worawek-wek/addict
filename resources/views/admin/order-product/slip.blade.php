<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จ #{{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #000 !important;
            text-shadow: none !important;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            font-size: 13px;
            font-weight: 500;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                background: white;
                padding: 0;
                display: block;
                width: 80mm;
                font-size: 13px;
            }
        }

        .receipt {
            background-color: white;
            width: 68mm;
            max-width: 68mm;
            padding: 10px 8px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        @media print {
            .receipt {
                box-shadow: none;
                width: 68mm;
                max-width: 68mm;
                padding: 0;
                margin: 0 auto;
            }
        }

        /* Watermark for unpaid */
        .unpaid-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 48px;
            font-weight: bold;
            color: rgba(220, 53, 69, 0.18);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            border: 4px solid rgba(220, 53, 69, 0.18);
            padding: 10px 20px;
        }

        .receipt > * { position: relative; z-index: 1; }

        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 { font-size: 18px; margin-bottom: 4px; font-weight: 800; }
        .header .subtitle { font-size: 13px; margin-bottom: 8px; font-weight: 700; }

        .info-line {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 12.5px;
            margin: 3px 0;
        }

        .section {
            margin: 12px 0;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            margin-bottom: 8px;
        }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td {
            font-size: 12px;
            padding: 4px 1px;
            vertical-align: top;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        th { border-bottom: 1px solid #000; text-align: left; font-weight: 800; }
        td.right, th.right { text-align: right; }
        td.center { text-align: center; }
        .col-index { width: 6mm; }
        .col-qty { width: 10mm; }
        .col-total { width: 17mm; }
        .product-name { padding-left: 1px; }

        .total-section { margin-top: 12px; }

        .total-line {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin: 5px 0;
        }

        .total-line.grand {
            font-size: 16px;
            font-weight: 800;
            border-top: 2px solid #000;
            padding-top: 8px;
            margin-top: 8px;
        }

        .payment-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .payment-badge.paid,
        .payment-badge.unpaid {
            background: transparent;
            border: 1px solid #000;
        }

        .footer {
            margin-top: 18px;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
        }

        @media print {
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; width: 68mm; max-width: 68mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt">

        @if (!$order->payment_status)
            <div class="unpaid-watermark">ยังไม่ชำระเงิน</div>
        @endif

        {{-- Header --}}
        <div class="header">
            <h1>Addict Coffee House</h1>
            <div class="subtitle">ใบเสร็จรับเงิน / Receipt</div>
            <div class="info-line">
                <span>เลขที่</span>
                <span><strong>{{ $order->order_number }}</strong></span>
            </div>
            <div class="info-line">
                <span>วันที่พิมพ์</span>
                <span>{{ date('d/m/Y H:i:s') }}</span>
            </div>
            <div class="info-line">
                <span>สาขา</span>
                <span>{{ $order->branch->name ?? '-' }}</span>
            </div>
            <div class="info-line">
                <span>พนักงานขาย</span>
                <span>{{ $order->seller->nickname ?? '-' }}</span>
            </div>
        </div>

        {{-- Items --}}
        <div class="section">
            <div class="section-title">รายการสินค้า</div>
            <table>
                <colgroup>
                    <col class="col-index">
                    <col>
                    <col class="col-qty">
                    <col class="col-total">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>สินค้า</th>
                        <th class="right">จำนวน</th>
                        <th class="right">รวม</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->products as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="product-name">{{ $item->product->name ?? '-' }}</td>
                            <td class="right">{{ $item->quantity }}</td>
                            <td class="right">{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;">ไม่มีสินค้า</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="total-section">
            @php
                $subtotal = $order->products->sum(fn($p) => $p->price * $p->quantity);
                $discount = $order->discount ?? 0;
                $total    = max(0, $subtotal - $discount);
            @endphp

            <div class="total-line">
                <span>Subtotal</span>
                <span>{{ number_format($subtotal, 2) }}</span>
            </div>

            @if ($discount > 0)
                <div class="total-line discount">
                    <span>ส่วนลด</span>
                    <span>- {{ number_format($discount, 2) }}</span>
                </div>
            @endif

            <div class="total-line grand">
                <span>ยอดรวมสุทธิ</span>
                <span>{{ number_format($total, 2) }} ฿</span>
            </div>
        </div>

        {{-- Payment status --}}
        <div style="text-align:center; margin-top:10px;">
            @if ($order->payment_status)
                <span class="payment-badge paid">
                    ชำระเงินแล้ว
                    @if($order->payment_method)
                        — {{ $order->payment_method }}
                    @endif
                </span>
            @else
                <span class="payment-badge unpaid">ยังไม่ชำระเงิน</span>
            @endif
        </div>

        <div class="footer">
            <p>ขอบคุณที่ใช้บริการ / Thank you for your business!</p>
        </div>

        {{-- Print button (hidden on actual print) --}}
        <div style="text-align:center; margin-top:20px;" class="no-print">
            <button onclick="window.print()"
                style="padding:8px 24px; background:#333; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                พิมพ์ใบเสร็จ
            </button>
            <button onclick="window.close()"
                style="padding:8px 24px; background:#ccc; color:#333; border:none; border-radius:6px; cursor:pointer; font-size:14px; margin-left:8px;">
                ปิด
            </button>
        </div>
    </div>

</body>
</html>
