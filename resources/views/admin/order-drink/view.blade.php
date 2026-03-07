<div class="modal-content border-0 rounded-3">
    <div class="modal-header border-0" style="background-color:#4cc9f0; color:white;">
        <h5 class="modal-title d-flex align-items-center">
            <i class="ti ti-file-description me-2"></i> รายละเอียดการสั่งซื้อ
            <span class="badge ms-3 {{ $orderDrink->badge_class ?? 'bg-secondary' }}">
                {{ $orderDrink->status_label ?? 'ไม่ระบุ' }}
            </span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body bg-light p-4">

        <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <div class="row mb-3">
                <div class="col-md-6"><strong class="me-2">สาขา:</strong> {{ $orderDrink->branch->name ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong class="me-2">ผู้ซื้อ:</strong> {{ $orderDrink->customer_type == 1 ? "พนักงาน":"ลูกค้า"; }}</div>
            </div>
            <div class="row mb-3">
                {{-- <div class="col-md-6"><strong class="me-2">พนักงานซื้อ:</strong> {{ $orderDrink->user->name ?? '-' }}</div> --}}
                <div class="col-md-6"><strong class="me-2">พนักงานขาย:</strong> {{ $orderDrink->seller->name ?? 'ONLINE' }}</div>
            </div>
            <div class="row mb-3">
                <!-- วิธีการชำระเงิน dropdown ย้ายไปด้านล่าง -->
            </div>
            <div class="row">
                <div class="col-md-4"><strong class="me-2">วันที่ซื้อ:</strong>
                    {{ \Carbon\Carbon::parse($orderDrink->booking_date)->format('d/m/Y') }}</div>
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
                    {{-- แสดงรายการสินค้าจากตะกร้า --}}
                    @foreach ($orderDrink->drinks as $item)
                        <tr>
                            <td>{{ $item->drink->name ?? 'สินค้าถูกลบ' }}</td>
                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                        <tr>
                            <td>ส่วนลด</td>
                            <td class="text-end"></td>
                            <td class="text-center"></td>
                            <td class="text-end">{{ number_format($orderDrink->discount, 2) }}</td>
                        </tr>
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end border-0">ยอดรวมสุทธิ</td>
                        <td class="text-end fs-5 border-0">{{ number_format($orderDrink->total_price, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Dropdown เปลี่ยนสถานะ --}}
        {{-- <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <label for="status" class="form-label">เปลี่ยนสถานะ</label>
            <select id="orderStatusSelect" class="form-select" data-id="{{ $orderDrink->id }}" data-current="{{ $orderDrink->ref_status_id }}">
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}"
                        {{ $orderDrink->ref_status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div> --}}
        {{-- <div class="bg-white p-3 rounded-3 shadow-sm">
            <label for="payment_method_select" class="form-label">วิธีการชำระเงิน</label>
            <form id="paymentMethodForm" action="#" method="post" onsubmit="return false;">
                <select class="form-select mt-1" id="payment_method_select" name="payment_method" data-id="{{ $orderDrink->id }}" @if($orderDrink->payment_method) disabled @endif>
                    <option value="">-- เลือกวิธีการชำระเงิน --</option>
                    <option value="cash" {{ $orderDrink->payment_method == 'cash' ? 'selected' : '' }}>เงินสด (Cash)</option>
                    <option value="โอน/สแกน QR Code (PromptPay)" {{ $orderDrink->payment_method == 'โอน/สแกน QR Code (PromptPay)' ? 'selected' : '' }}>โอน/สแกน QR Code (PromptPay)</option>
                    <option value="บัตรเครดิต/เดบิต (Credit/Debit Card)" {{ $orderDrink->payment_method == 'บัตรเครดิต/เดบิต (Credit/Debit Card)' ? 'selected' : '' }}>บัตรเครดิต/เดบิต (Credit/Debit Card)</option>
                    <option value="WeChat Pay" {{ $orderDrink->payment_method == 'WeChat Pay' ? 'selected' : '' }}>WeChat Pay</option>
                    <option value="Alipay" {{ $orderDrink->payment_method == 'Alipay' ? 'selected' : '' }}>Alipay</option>
                    <option value="TrueMoney Wallet / LINE Pay (E-Wallet)" {{ $orderDrink->payment_method == 'TrueMoney Wallet / LINE Pay (E-Wallet)' ? 'selected' : '' }}>TrueMoney Wallet / LINE Pay (E-Wallet)</option>
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
