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
<span style="font-size: 12px; font-weight: bold;">รายงานคูปองพนักงาน วันที่ {{ date('d/m/Y') }}  , เวลา {{ date('H:i') }}</span>

<table class="table table-striped">
    <thead>
        <tr>
            {{-- <th style="width: 8%;">ลำดับ</th> --}}
            <th class="text-center">วันที่</th>
            <th class="text-center">เวลา</th>
            <th class="text-center">ชื่อพนักงาน</th>
            <th class="text-center">ชม.</th>
            <th class="text-center">จำนวนลูกค้า</th>
            <th class="text-center">@ราคา</th>
            <th class="text-center">รวมเงิน</th>
            <th class="text-center">รหัสผู้ดูแล</th>
            <th class="text-center">ชื่อผู้ดูแล</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sumTotal = 0;
            $sumCustomer = 0;
            $orders = $orderRooms->values();
        @endphp

        @foreach ($orders as $order)
            @php
                $sumTotal += $order->total_price;
                $next = $loop->last ? null : $orders->get($loop->index + 1);
                $sumCustomer++;
            @endphp

            <tr>
                <td class="text-center">{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                <td class="text-center">{{ date('H:i', strtotime($order->created_at)) }}</td>
                <td class="text-center">{{ @$order->user->name." + ".@$order->course->name }}</td>

                <td class="text-center">
                    @php
                        $start = \Carbon\Carbon::parse($order->start_time);
                        $end   = \Carbon\Carbon::parse($order->end_time);
                        $diff  = $start->diff($end);
                    @endphp

                    @if($diff->h > 0) {{ $diff->h }} ชม. @endif
                    @if($diff->i > 0) {{ $diff->i }} นาที @endif
                </td>

                <td class="text-center">1</td>
                <td class="text-center">{{ number_format($order->total_price) }}</td>
                <td class="text-center">{{ number_format($order->total_price) }}</td>
                <td class="text-center">{{ @$order->seller->user_id }}</td>
                <td class="text-center">{{ @$order->seller->name }}</td>
            </tr>

            {{-- ✅ ถ้า user ถัดไปไม่ใช่คนเดียวกัน ให้สรุปยอด --}}
            @if (!$next || $next->ref_user_id != $order->ref_user_id)
                <tr class="table-warning fw-bold" style="background-color: #fff7f0;">
                    <th colspan="4" class="text-start">รวมยอด</th>
                    <th class="text-center">{{ $sumCustomer }}</th>
                    <th class="text-center"></th>
                    <th class="text-center">{{ number_format($sumTotal) }}</th>
                    <th colspan="2"></th>
                </tr>
                <tr>
                    <th colspan="9">&nbsp;</th>
                </tr>
                @php
                    $sumTotal = 0;
                    $sumCustomer = 0;
                @endphp
            @endif
        @endforeach
        <tr>
            <th colspan="6" class="text-end">รวมทั้งหมด</th>
            <th class="text-center">{{ number_format($summary_total_price,2) }}</th>
            <th colspan="4"></th>
        </tr>

        @if ($orders->isEmpty())
            <tr>
                <td colspan="9" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @endif

    </tbody>
</table>
