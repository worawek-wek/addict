<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จ #{{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: 80mm auto;   /* thermal slip width, height auto */
            margin: 4mm 4mm;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #f5f5f5;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                display: block;
            }
        }

        .receipt {
            background-color: white;
            width: 100%;
            max-width: 280px;
            padding: 10px 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }

        @media print {
            .receipt {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
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
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h1 { font-size: 20px; margin-bottom: 4px; }
        .header .subtitle { font-size: 13px; color: #555; margin-bottom: 8px; }

        .info-line {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 3px 0;
        }

        .section {
            margin: 12px 0;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            text-align: center;
            margin-bottom: 8px;
        }

        table { width: 100%; border-collapse: collapse; }
        th, td { font-size: 12px; padding: 3px 2px; }
        th { border-bottom: 1px solid #ccc; text-align: left; }
        td.right, th.right { text-align: right; }
        td.center { text-align: center; }

        .total-section { margin-top: 12px; }

        .total-line {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin: 5px 0;
        }

        .total-line.discount { color: #c00; }

        .total-line.grand {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
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

        .payment-badge.paid { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .payment-badge.unpaid { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }

        .footer {
            margin-top: 18px;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        @media print {
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; max-width: 100%; }
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
                <thead>
                    <tr>
                        <th>#</th>
                        <th>สินค้า</th>
                        <th class="right">จำนวน</th>
                        <th class="right">ราคา</th>
                        <th class="right">รวม</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->products as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td class="right">{{ $item->quantity }}</td>
                            <td class="right">{{ number_format($item->price, 2) }}</td>
                            <td class="right">{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center; color:#999;">ไม่มีสินค้า</td></tr>
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
                    ✓ ชำระเงินแล้ว
                    @if($order->payment_method)
                        — {{ $order->payment_method }}
                    @endif
                </span>
            @else
                <span class="payment-badge unpaid">⚠ ยังไม่ชำระเงิน</span>
            @endif
        </div>

        <div class="footer">
            <p>ขอบคุณที่ใช้บริการ / Thank you for your business!</p>
        </div>

        {{-- Print button (hidden on actual print) --}}
        <div style="text-align:center; margin-top:20px;" class="no-print">
            <button onclick="window.print()"
                style="padding:8px 24px; background:#333; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                🖨️ พิมพ์ใบเสร็จ
            </button>
            <button onclick="window.close()"
                style="padding:8px 24px; background:#ccc; color:#333; border:none; border-radius:6px; cursor:pointer; font-size:14px; margin-left:8px;">
                ✕ ปิด
            </button>
        </div>
    </div>

    <script>
        // Always auto-print (loaded inside hidden iframe by parent page)
        window.onload = function () { window.print(); };
    </script>
</body>
</html>
