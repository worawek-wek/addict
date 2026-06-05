<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>สาขา</th>
            <th>ลูกค้า</th>
            <th>ชื่อเด็ก</th>
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
            @php
                $canManage = $order->can_manage ?? false;
            @endphp
            <tr>
                <td>{{ $loop->iteration + (($orderRooms->currentPage() - 1) * $orderRooms->perPage()) }}</td>
                <td>{{ $order->branch->name ?? '-' }}</td>
                <td>{{ $order->customer->name ?? '-' }}</td>
                <td>{{ $order->user->name ?? '-' }}</td>
                <td>{{ $order->room->name ?? '-' }}</td>
                <td>{{ $order->booking_date }}</td>
                <td>{{ $order->start_time }}</td>
                <td>{{ $order->end_time }}</td>
                <td><span class="badge {{ $order->status->color }}">{{ $order->status->name }}</span></td>
                <td>
                    <div class="d-flex gap-3">
                        <div class="dropdown">
                            <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="actionDropdown{{ $order->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                จัดการ
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="actionDropdown{{ $order->id }}">
                                <li><a class="dropdown-item" href="#" onclick="view({{ $order->id }}); return false;">ดู</a></li>
                                <li><a class="dropdown-item" href="#" onclick="printReceipt({{ $order->id }}); return false;">ปริ้นใบเสร็จ</a></li>
                                @if ($order->ref_status_id != 4 && $canManage)
                                    <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder({{ $order->id }}); return false;">ยกเลิกการจอง</a></li>
                                @endif
                                @if (!$canManage)
                                    <li><span class="dropdown-item text-muted">ดู/พิมพ์ได้เท่านั้น</span></li>
                                @endif
                            </ul>
                        </div>
                        @if (in_array($order->ref_status_id,[1,4]) && $canManage)
                            <a href="javascript:;"
                                class="btn btn-xs rounded-pill btn-danger d-flex align-items-center gap-1 py-1"
                                onclick='Delete({{ $order->id }})'
                                data-bs-toggle="modal"
                                data-bs-target="#delete_confirmation_modal">
                                    <i class="fa fa-trash"></i>
                                    ลบ
                            </a>
                        @endif
                    </div>
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
                        loadData(page);
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
