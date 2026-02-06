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
                <td colspan="10" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @endif

    </tbody>
</table>
