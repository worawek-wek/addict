<table class="table table-striped">
    <thead>
        <tr class="table-info">
            <th style="width: 5%;">#</th>
            <th style="width: 5%;">ห้อง</th>
            <th style="width: 5%;">วันที่</th>
            <th style="width: 8%;">เวลา</th>
            <th style="width: 5%;">ชม.</th>
            <th style="width: 6%;">ชำระเงิน</th>
            <th style="width: 10%;">ค่านวด</th>
            <th style="width: 10%;">อาหาร</th>
            <th style="width: 10%;">เครื่องดื่มพนักงาน</th>
            <th style="width: 10%;">เครื่องดื่มลูกค้า</th>
            <th style="width: 10%;">รวมเงิน</th>
            <th style="width: 8%;">คูปอง</th>
            <th style="width: 8%;">รับจริงของร้าน</th>
            <th style="width: 8%;">สถานะ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderRooms as $order)
            <tr>
                <td>{{ $loop->iteration + (($orderRooms->currentPage() - 1) * $orderRooms->perPage()) }}</td>
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

<script>
function cancelOrder(orderId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิกการจอง?',
        text: 'คุณต้องการยกเลิกการจองนี้หรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ยกเลิกการจอง',
        cancelButtonText: 'ไม่ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/order-rooms/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status_id: 4 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('สำเร็จ!', 'ยกเลิกการจองเรียบร้อย', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด!', data.message || 'ไม่สามารถยกเลิกการจองได้', 'error');
                }
            });
        }
    });
}
</script>
</table>

{{-- Pagination --}}
<div class="mt-3">
    {!! $orderRooms->links('vendor.pagination.custom') !!}
</div>
