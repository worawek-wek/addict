<table class="table table-striped">
    <thead>
        <tr>
            {{-- <th style="width: 8%;">ลำดับ</th> --}}
            <th class="text-center" style="width: 8%;">วันที่</th>
            <th class="text-center" style="width: 8%;">เวลา</th>
            <th class="text-center" style="width: 20%;">ร้อยนัดราน</th>
            <th class="text-center" style="width: 6%;">ชม.</th>
            <th class="text-center" style="width: 6%;">จำนวนลูกค้า</th>
            <th class="text-center" style="width: 10%;">@ราคา</th>
            <th class="text-center" style="width: 10%;">รวมเงิน</th>
            <th class="text-center" style="width: 10%;">รหัสผู้ดูแล</th>
            <th class="text-center" style="width: 28%;">ชื่อผู้ดูแล</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sumTotal = 0;
            $sumCustomer = 0;
        @endphp

        @foreach ($orderRooms as $order)
            @php
                $sumTotal += $order->total_price;
                $next = $orderRooms[$loop->index + 1] ?? null;
                $sumCustomer++
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
                <tr class="table-warning fw-bold">
                    <td colspan="4" class="text-start">รวมยอด</td>
                    <td class="text-center">{{ $sumCustomer }}</td>
                    <td class="text-center"></td>
                    <td class="text-center">{{ number_format($sumTotal) }}</td>
                    <td colspan="2"></td>
                </tr>

                @php
                    $sumTotal = 0;
                    $sumCustomer = 0;
                @endphp
            @endif
        @endforeach
        @if ($orderRooms->isEmpty())
            <tr>
                <td class="text-center" colspan="10" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @endif

    </tbody>
</table>

{{-- Pagination --}}
<div class="mt-3">
    {!! $orderRooms->links('vendor.pagination.custom') !!}
</div>
