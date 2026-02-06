<table class="table table-striped">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">คำสั่งซื้อ</th>
            <th class="text-center">สาขา</th>
            <th class="text-center">พนักงานขาย</th>
            <th class="text-center">ยอดรวมสุทธิ</th>
            <th class="text-center">สถานะ</th>
            <th class="text-center">ช่องทางชำระเงิน</th>
            <th class="text-center">จัดการ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderProducts as $order)
            <tr>
                <td class="text-center">{{ $loop->iteration + (($orderProducts->currentPage() - 1) * $orderProducts->perPage()) }}</td>
                <td class="text-center">{{ $order->order_number ?? '-' }}</td>
                <td class="text-center">{{ $order->branch->name ?? '-' }}</td>
                <td class="text-center">{{ $order->seller->nickname ?? '-' }}</td>
                <td class="text-center">{{ $order->total_price }}</td>
                <td class="text-center">@if($order->payment_status == 0) <span class="badge bg-warning">ยังไม่ชำระเงิน</span> @else <span class="badge bg-success">ชำระเงินแล้ว</span> @endif </td>
                <td class="text-center"><span class="badge {{ $order->badge_class }}">{{ $order->status_label }}</span></td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="actionDropdown{{ $order->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                            จัดการ
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="actionDropdown{{ $order->id }}">
                            <li><a class="dropdown-item" href="#" onclick="view({{ $order->id }}); return false;">ดู</a></li>
                            @if ($order->payment_status == 0)
                                <li><a class="dropdown-item text-success" href="#" onclick="confirmOrder({{ $order->id }}); return false;">ยืนยันชำระเงิน</a></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder({{ $order->id }}); return false;">ยกเลิกการจอง</a></li>
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        @if ($orderProducts->isEmpty())
            <tr>
                <td colspan="10" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @endif

    </tbody>
</table>

<script>
function confirmOrder(orderId) {
    Swal.fire({
        title: 'ยืนยันการชำระเงิน?',
        text: 'คุณต้องการชำระเงินนี้หรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ชำระเงิน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/order-products/${orderId}/confirm-payment`, {
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
                    Swal.fire('สำเร็จ!', 'ชำระเงินเรียบร้อย', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด!', data.message || 'ไม่สามารถชำระเงินได้', 'error');
                }
            });
        }
    });
}
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
    {!! $orderProducts->links('vendor.pagination.custom') !!}
</div>
