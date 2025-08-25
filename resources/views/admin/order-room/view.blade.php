<div class="modal-content border-0 rounded-3">
    <div class="modal-header border-0" style="background-color:#4cc9f0; color:white;">
        <h5 class="modal-title d-flex align-items-center">
            <i class="ti ti-file-description me-2"></i> รายละเอียดการจองห้อง
            <span class="badge ms-3 {{ $orderRoom->badge_class ?? 'bg-secondary' }}">
                {{ $orderRoom->status_label ?? 'ไม่ระบุ' }}
            </span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body bg-light p-4">
        {{-- ข้อมูลต่าง ๆ --}}
        <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <div class="row mb-3">
                <div class="col-md-6"><strong>สาขา:</strong> {{ $orderRoom->branch->name ?? '-' }}</div>
                <div class="col-md-6"><strong>ห้อง:</strong> {{ $orderRoom->room->name ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>ลูกค้า:</strong> {{ $orderRoom->customer->name ?? 'Walk-in' }}</div>
                <div class="col-md-6"><strong>เบอร์โทร:</strong> {{ $orderRoom->customer->phone ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>พนักงานนวด:</strong> {{ $orderRoom->user->name ?? '-' }}</div>
                <div class="col-md-6"><strong>พนักงานขาย:</strong> {{ $orderRoom->seller->name ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>วันที่จอง:</strong>
                    {{ \Carbon\Carbon::parse($orderRoom->booking_date)->format('d/m/Y') }}</div>
                <div class="col-md-4"><strong>เวลาเช็คอิน:</strong>
                    {{ \Carbon\Carbon::parse($orderRoom->start_time)->format('H:i') }}</div>
                <div class="col-md-4"><strong>เวลาเช็คเอาท์:</strong>
                    {{ \Carbon\Carbon::parse($orderRoom->end_time)->format('H:i') }}</div>
            </div>
        </div>

        {{-- ตารางแสดงรายการสินค้า --}}
        <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <h6 class="border-bottom pb-2 mb-3 fw-bold">รายการทั้งหมด</h6>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>รายการ</th>
                        <th class="text-end">ราคา/หน่วย</th>
                        <th class="text-center">จำนวน</th>
                        <th class="text-end">ราคารวม</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- แสดงราคาห้อง --}}
                    @if ($orderRoom->service_laundry_cost && $orderRoom->room)
                        @php
                            $priceColumn = $orderRoom->service_laundry_cost;
                            $roomPrice = $orderRoom->room->{$priceColumn} ?? 0;
                            $duration = match($priceColumn) {
                                'forty_minutes' => 40,
                                'sixty_minutes' => 60,
                                'ninety_minutes' => 90,
                                default => ''
                            };
                        @endphp
                        <tr>
                            <td>ค่าบริการห้อง ({{ $orderRoom->room->name }}) - {{ $duration }} นาที</td>
                            <td class="text-end">{{ number_format($roomPrice, 2) }}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ number_format($roomPrice, 2) }}</td>
                        </tr>
                    @endif

                    {{-- ▼▼▼ เพิ่มโค้ดส่วนนี้สำหรับแสดงราคาพนักงานนวด ▼▼▼ --}}
                    @if ($orderRoom->user && $orderRoom->user->salary)
                        <tr>
                            <td>ค่าบริการพนักงาน ({{ $orderRoom->user->name }})</td>
                            <td class="text-end">{{ number_format($orderRoom->user->salary, 2) }}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ number_format($orderRoom->user->salary, 2) }}</td>
                        </tr>
                    @endif
                    {{-- ▲▲▲ สิ้นสุดส่วนแสดงราคาพนักงานนวด ▲▲▲ --}}

                    {{-- แสดงรายการสินค้าจากตะกร้า --}}
                    @foreach ($orderRoom->products as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'สินค้าถูกลบ' }}</td>
                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach

                    {{-- แสดงรายการ Addons --}}
                    @foreach ($orderRoom->addons as $addonItem)
                        <tr>
                            <td>{{ $addonItem->option->name ?? 'Addon ถูกลบ' }}</td>
                            <td class="text-end">{{ number_format($addonItem->price, 2) }}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ number_format($addonItem->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end border-0">ยอดรวมสุทธิ</td>
                        <td class="text-end fs-5 border-0">{{ number_format($orderRoom->total_price, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Dropdown เปลี่ยนสถานะ --}}
        <div class="bg-white p-3 rounded-3 shadow-sm">
            <label for="status" class="form-label">เปลี่ยนสถานะ</label>
            <select id="orderStatusSelect" class="form-select" data-id="{{ $orderRoom->id }}" data-current="{{ $orderRoom->ref_status_id }}">
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}"
                        {{ $orderRoom->ref_status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
    document.getElementById('orderStatusSelect')?.addEventListener('change', function() {
        let orderId = this.getAttribute('data-id');
        let statusId = this.value;
        let selectEl = this;
        let originalStatusId = this.getAttribute('data-current');

        Swal.fire({
            title: 'ยืนยันการเปลี่ยนสถานะ?',
            text: "คุณแน่ใจหรือไม่ที่จะเปลี่ยนสถานะนี้",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, เปลี่ยนเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/order-rooms/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status_id: statusId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('สำเร็จ!', "สถานะถูกเปลี่ยนเป็น " + data.status, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('ผิดพลาด!', data.message || 'ไม่สามารถเปลี่ยนสถานะได้', 'error');
                        selectEl.value = originalStatusId;
                    }
                });
            } else {
                selectEl.value = originalStatusId;
            }
        });
    });
</script>
