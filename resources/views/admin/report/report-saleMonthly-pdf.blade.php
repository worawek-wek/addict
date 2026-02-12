<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    th, td {
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: center;
    }
    thead th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
</style>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>ห้อง</th>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>ชม.</th>
            <th>ชำระเงิน</th>
            <th>ค่านวด</th>
            <th>อาหาร</th>
            <th>เครื่องดื่มพนักงาน</th>
            <th>เครื่องดื่มลูกค้า</th>
            <th>รวมเงิน</th>
            <th>คูปอง</th>
            <th>รับจริงของร้าน</th>
            <th>สถานะ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderRooms as $key => $order)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $order->room_type->name ?? '-' }}</td>
                <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                <td>{{ date('h:i', strtotime($order->created_at)) }}</td>
                <td>
                    @php
                        $start = \Carbon\Carbon::parse($order->start_time);
                        $end   = \Carbon\Carbon::parse($order->end_time);

                        $diff = $start->diff($end);
                    @endphp

                    @if($diff->h > 0){{ $diff->h }} ชม. @endif @if($diff->i > 0) {{ $diff->i }} นาที @endif
                </td>
                <td>{{ $order->status_label }}</td>
                <td>{{ number_format($order->addons_sum_price ?? 0)}}</td>
                <td> 0 </td>
                <td> 0 </td>
                <td>{{ number_format($order->products_sum_price ?? 0)}}</td>
                <td>{{ number_format($order->total_price) }}</td>
                <td>{{ number_format($order->addons_sum_coupon ?? 0)}}</td>
                <td>{{ number_format($order->total_price - $order->addons_sum_coupon) }}</td>
                <td>
                    {{ $order->status->name }}
                    {{-- @if ($order->payment_status == 2)
                        ยกเลิก
                    @endif --}}
                </td>
            </tr>
        @endforeach
        @if ($orderRooms->isEmpty())
            <tr>
                <td colspan="14" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @else
            <!-- Summary Row -->
            <tr style="font-weight: bold; background: #e0e0e0;">
                <td colspan="6" style="text-align: right;">รวมยอดทั้งหมด</td>
                <td>{{ number_format($discounts_summary ?? 0) }}</td>
                <td>{{ number_format($addons_sum_price ?? 0) }}</td>
                <td></td>
                <td></td>
                <td>{{ number_format($summary_receive_price ?? 0) }}</td>
                <td></td>
                <td>{{ number_format($summary_receive_price_after_discount ?? 0) }}</td>
                <td></td>
            </tr>
        @endif

    </tbody>
</table>

<!-- สรุปยอดรวมทั้งหมด (Summary Box) -->
<div style="margin-top:30px; width:100%;">
    <table style="width: 60%; margin: 0 auto; border: 2px solid #333; font-size: 12px;">
        <tr style="background: #f7f7f7; font-weight: bold;">
            <td colspan="2" style="text-align:center; border-bottom:2px solid #333;">สรุปยอดรวมประจำเดือน</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรวมส่วนลด (Discount)</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($discounts_summary ?? 0, 2) }} บาท</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรวม Addon</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($addons_sum_price ?? 0, 2) }} บาท</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรับจริงก่อนหักส่วนลด</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($summary_receive_price ?? 0, 2) }} บาท</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรับจริงหลังหักส่วนลด</td>
            <td style="text-align:right; padding-right: 20px; font-weight:bold; color:#1a8917;">{{ number_format($summary_receive_price_after_discount ?? 0, 2) }} บาท</td>
        </tr>
    </table>
</div>
