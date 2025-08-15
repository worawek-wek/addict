<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>สาขา</th>
            <th>ลูกค้า</th>
            <th>ห้อง</th>
            <th>วันที่จอง</th>
            <th>เวลาเช็คอิน</th>
            <th>เวลาเช็คเอาท์</th>
            <th>สถานะ</th>
            <th class="text-center">จัดการ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderRooms as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->branch->name ?? '-' }}</td>
                <td>{{ $order->customer->name ?? '-' }}</td>
                <td>{{ $order->room->name ?? '-' }}</td>
                <td>{{ $order->booking_date }}</td>
                <td>{{ $order->start_time }}</td>
                <td>{{ $order->end_time }}</td>
                <td><span class="badge {{ $order->badge_class }}">{{ $order->status_label }}</span></td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="view({{ $order->id }})">ดู</button>
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

{{-- Pagination --}}
<div class="mt-3">
    {!! $orderRooms->links('vendor.pagination.custom') !!}
</div>
