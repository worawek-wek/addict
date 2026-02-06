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
<table class="table table-striped">
    <thead>
        <tr>
            <th>ลำดับ</th>
            <th>ห้อง</th>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>รหัสผู้ดูแล</th>
            <th>ชื่อผู้ดูแล</th>
            <th>ชื่อพนักงาน</th>
            <th>นาที</th>
            <th>@ราคา</th>
            <th>ราคาเต็ม</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderRooms as $key => $order)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $order->room_type->name ?? '-' }}</td>
                <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                <td>{{ date('h:i', strtotime($order->created_at)) }}</td>
                <td>{{ @$order->seller->user_id }}</td>
                <td>{{ @$order->seller->name }}</td>
                <td>{{ @$order->user->name }}</td>
                {{-- <td>{{ number_format($order->addons_sum_price ?? 0)}}</td> --}}
                <td>
                    @php
                        $start = \Carbon\Carbon::parse($order->start_time);
                        $end   = \Carbon\Carbon::parse($order->end_time);

                        $diff = $start->diff($end);
                    @endphp

                    @if($diff->h > 0){{ $diff->h }} ชม. @endif @if($diff->i > 0) {{ $diff->i }} นาที @endif
                </td>
                <td>{{ number_format($order->total_price) }}</td>
                <td>{{ number_format($order->total_price) }}</td>
            </tr>
        @endforeach
        @if ($orderRooms->isEmpty())
            <tr>
                <td colspan="10" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @endif

    </tbody>
</table>