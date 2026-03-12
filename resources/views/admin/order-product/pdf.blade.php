<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Addict Coffee House - Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .receipt {
            background-color: white;
            width: 100%;
            max-width: 400px;
            padding: 30px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header .thai-subtitle {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .info-line {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 3px 0;
        }

        .section {
            margin: 15px 0;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: center;
        }

        .line-item {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 5px 0;
            padding: 2px 0;
        }

        .total-section {
            margin-top: 15px;
            padding-top: 10px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0;
        }

        .grand-total {
            font-size: 16px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>Addict Coffee House</h1>
            <div class="thai-subtitle">รายงานสรุปยอดขาย</div>
            <div class="info-line">
                <span>พิมพ์โดย</span>
                <span>AD</span>
            </div>
            <div class="info-line">
                <span>วันที่พิมพ์</span>
                <span>{{ date('d/m/Y H:i:s') }}</span>
            </div>
            <div class="info-line">
                <span>{{ $date_before.' - '.date('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        @php
            // ประกาศตัวแปรไว้ก่อนเพื่อป้องกัน Error
            $total_price = 0;
            $total_cost = 0;
            $payment_total_price = 0;
        @endphp

        <div class="section">
            <div class="section-title">สรุปยอดขาย</div>
            <b>พนักงาน</b>
            <table width="100%">
                <tbody>
                @foreach ($product_employee as $key => $item)
                @php
                    $total_price += $item->total_price;
                    $total_cost += $item->total_cost ?? 0;
                @endphp
                    <tr>
                        <td width="10%">{{ $key+1 }}</td>
                        <td width="50%">{{ $item->type_name ?? 'ไม่ระบุประเภท' }}</td>
                        <td width="20%">{{ number_format($item->total_qty) }} ชิ้น</td>
                        <td width="20%" align="right">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>
            <b>ลูกค้า</b>
            <table width="100%">
                <tbody>
                @foreach ($product_customer as $key => $item2)
                @php
                    $total_price += $item2->total_price;
                    $total_cost += $item2->total_cost ?? 0;
                @endphp
                    <tr>
                        <td width="10%">{{ $key+1 }}</td>
                        <td width="50%">{{ $item2->type_name ?? 'ไม่ระบุประเภท' }}</td>
                        <td width="20%">{{ number_format($item2->total_qty) }} ชิ้น</td>
                        <td width="20%" align="right">{{ number_format($item2->total_price, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-line grand-total">
                <span>รวมยอดเงินที่ได้รับ</span>
                <span>{{ number_format($total_price, 2) }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">ช่องทางการชำระเงิน</div>
            <table width="100%">
                <tbody>
                @foreach ($payment_channel as $key => $item3)
                @php
                    $payment_total_price += $item3->total_price;
                @endphp
                    <tr>
                        <td width="10%">{{ $key+1 }}</td>
                        <td width="70%">{{ $item3->payment_method }}</td>
                        <td width="20%" align="right">{{ number_format($item3->total_price, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">สรุปต้นทุน</div>
            <table width="100%">
                <tbody>
                    <tr>
                        <td width="10%">1</td>
                        <td width="70%">ต้นทุน</td>
                        <td width="20%" align="right">{{ number_format($total_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>ยอดขาย</td>
                        <td align="right">{{ number_format($payment_total_price, 2) }}</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>กำไร</td>
                        <td align="right">{{ number_format($payment_total_price - $total_cost, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
