<div class="modal-content border-0 rounded-3">
    <div class="modal-header border-0" style="background-color:#4cc9f0; color:white;">
        <h5 class="modal-title d-flex align-items-center">
            <i class="ti ti-file-description me-2"></i> รายละเอียดการสั่งซื้อ
            <span class="badge ms-3 {{ $orderProduct->badge_class ?? 'bg-secondary' }}">
                {{ $orderProduct->status_label ?? 'ไม่ระบุ' }}
            </span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body bg-light p-4">

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
                    @if ($orderProduct->service_laundry_cost && $orderProduct->room)
                        @php
                            $priceColumn = $orderProduct->service_laundry_cost;
                            $roomPrice = $orderProduct->room->{$priceColumn} ?? 0;
                            $duration = match($priceColumn) {
                                'forty_minutes' => 40,
                                'sixty_minutes' => 60,
                                'ninety_minutes' => 90,
                                default => ''
                            };
                        @endphp
                        <tr>
                            <td>ค่าบริการห้อง ({{ $orderProduct->room->name }}) - {{ $duration }} นาที</td>
                            <td class="text-end">{{ number_format($roomPrice, 2) }}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ number_format($roomPrice, 2) }}</td>
                        </tr>
                    @endif

                    {{-- ▼▼▼ เพิ่มโค้ดส่วนนี้สำหรับแสดงราคาพนักงานนวด ▼▼▼ --}}
                    @if ($orderProduct->user && $orderProduct->user->salary)
                        <tr>
                            <td>ค่าบริการพนักงาน ({{ $orderProduct->user->name }})</td>
                            <td class="text-end">{{ number_format($orderProduct->user->salary, 2) }}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ number_format($orderProduct->user->salary, 2) }}</td>
                        </tr>
                    @endif
                    {{-- ▲▲▲ สิ้นสุดส่วนแสดงราคาพนักงานนวด ▲▲▲ --}}

                    {{-- แสดงรายการสินค้าจากตะกร้า --}}
                    @foreach ($orderProduct->products as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'สินค้าถูกลบ' }}</td>
                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach

                    {{-- แสดงรายการ Addons --}}
                    @foreach ($orderProduct->addons as $addonItem)
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
                        <td class="text-end fs-5 border-0">{{ number_format($orderProduct->total_price, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Dropdown เปลี่ยนสถานะ --}}
        {{-- <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <label for="status" class="form-label">เปลี่ยนสถานะ</label>
            <select id="orderStatusSelect" class="form-select" data-id="{{ $orderProduct->id }}" data-current="{{ $orderProduct->ref_status_id }}">
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}"
                        {{ $orderProduct->ref_status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div> --}}
        {{-- <div class="bg-white p-3 rounded-3 shadow-sm">
            <label for="payment_method_select" class="form-label">วิธีการชำระเงิน</label>
            <form id="paymentMethodForm" action="#" method="post" onsubmit="return false;">
                <select class="form-select mt-1" id="payment_method_select" name="payment_method" data-id="{{ $orderProduct->id }}" @if($orderProduct->payment_method) disabled @endif>
                    <option value="">-- เลือกวิธีการชำระเงิน --</option>
                    <option value="cash" {{ $orderProduct->payment_method == 'cash' ? 'selected' : '' }}>เงินสด (Cash)</option>
                    <option value="โอน/สแกน QR Code (PromptPay)" {{ $orderProduct->payment_method == 'โอน/สแกน QR Code (PromptPay)' ? 'selected' : '' }}>โอน/สแกน QR Code (PromptPay)</option>
                    <option value="บัตรเครดิต/เดบิต (Credit/Debit Card)" {{ $orderProduct->payment_method == 'บัตรเครดิต/เดบิต (Credit/Debit Card)' ? 'selected' : '' }}>บัตรเครดิต/เดบิต (Credit/Debit Card)</option>
                    <option value="WeChat Pay" {{ $orderProduct->payment_method == 'WeChat Pay' ? 'selected' : '' }}>WeChat Pay</option>
                    <option value="Alipay" {{ $orderProduct->payment_method == 'Alipay' ? 'selected' : '' }}>Alipay</option>
                    <option value="TrueMoney Wallet / LINE Pay (E-Wallet)" {{ $orderProduct->payment_method == 'TrueMoney Wallet / LINE Pay (E-Wallet)' ? 'selected' : '' }}>TrueMoney Wallet / LINE Pay (E-Wallet)</option>
                </select>
            </form>
        </div> --}}
    </div>
</div>

{{-- Script --}}
<script>
    // Payment method change handler
    document.getElementById('payment_method_select')?.addEventListener('change', function() {
        let orderId = this.getAttribute('data-id');
        let paymentMethod = this.value;
        if (!orderId) return;
        fetch(`/admin/order-rooms/${orderId}/update-payment-method`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ payment_method: paymentMethod })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('สำเร็จ!', 'อัปเดตวิธีการชำระเงินเรียบร้อย', 'success').then(() => {
                    window.location.href = '{{ route('order-rooms.index') }}';
                });
            } else {
                Swal.fire('ผิดพลาด!', data.message || 'ไม่สามารถอัปเดตวิธีการชำระเงินได้', 'error');
            }
        });
    });
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
