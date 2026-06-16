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
        {{-- ข้อมูลการจอง --}}
        <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <h6 class="border-bottom pb-2 mb-3 fw-bold">
                <i class="ti ti-calendar-event me-2"></i>ข้อมูลการจอง
            </h6>
            <div class="row mb-3">
                <div class="col-md-6"><strong>สาขา:</strong> {{ $orderRoom->branch->name ?? '-' }}</div>
                <div class="col-md-6"><strong>ห้อง:</strong> {{ $orderRoom->room->name ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>พนักงานนวด:</strong> {{ $orderRoom->user->name ?? '-' }}</div>
                <div class="col-md-6"><strong>พนักงานขาย:</strong> {{ $orderRoom->seller->name ?? 'ONLINE' }}</div>
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

        {{-- ข้อมูลลูกค้า --}}
        @if($orderRoom->customer && $orderRoom->ref_status_id == 1)
        <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <h6 class="border-bottom pb-2 mb-3 fw-bold">
                <i class="ti ti-user me-2"></i>ข้อมูลลูกค้า
            </h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong><i class="ti ti-user-circle me-1"></i>ชื่อ-นามสกุล:</strong>
                    {{ $orderRoom->customer->name ?? '-' }}
                </div>
                <div class="col-md-6">
                    <strong><i class="ti ti-flag me-1"></i>สัญชาติ:</strong>
                    {{ $orderRoom->customer->nationality ?? '-' }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong><i class="ti ti-phone me-1"></i>เบอร์โทร:</strong>
                    {{ $orderRoom->customer->phone ?? '-' }}
                </div>
            </div>

            {{-- ช่องทางติดต่อ --}}
            @php
                $contacts = [
                    'contact_line' => ['icon' => 'fa-brands fa-line', 'label' => 'LINE', 'color' => 'success'],
                    'contact_whatsapp' => ['icon' => 'fa-brands fa-whatsapp', 'label' => 'WhatsApp', 'color' => 'success'],
                    'contact_wechat' => ['icon' => 'fa-brands fa-weixin', 'label' => 'WeChat', 'color' => 'success'],
                    'contact_telegram' => ['icon' => 'fa-brands fa-telegram', 'label' => 'Telegram', 'color' => 'info'],
                    'contact_email' => ['icon' => 'fa-regular fa-envelope', 'label' => 'Email', 'color' => 'secondary'],
                ];
                $hasContact = false;
                foreach($contacts as $key => $info) {
                    if(!empty($orderRoom->customer->$key)) {
                        $hasContact = true;
                        break;
                    }
                }
            @endphp

            @if($hasContact)
            <div class="mt-3 pt-3 border-top">
                <strong class="d-block mb-2"><i class="ti ti-message-circle me-1"></i>ช่องทางติดต่อ:</strong>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($contacts as $key => $info)
                        @if(!empty($orderRoom->customer->$key))
                            <span class="badge bg-{{ $info['color'] }} bg-opacity-10 text-{{ $info['color'] }} px-3 py-2">
                                <i class="{{ $info['icon'] }} me-1"></i>
                                {{ $info['label'] }}: <strong>{{ $orderRoom->customer->$key }}</strong>
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
            @else
            <div class="mt-3 pt-3 border-top">
                <span class="text-muted"><i class="ti ti-info-circle me-1"></i>ไม่มีข้อมูลช่องทางติดต่อ</span>
            </div>
            @endif
        </div>
        @else
        <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <h6 class="border-bottom pb-2 mb-3 fw-bold">
                <i class="ti ti-user me-2"></i>ข้อมูลลูกค้า
            </h6>
            <p class="text-muted mb-0"><i class="ti ti-walk me-1"></i>Walk-in (ไม่มีข้อมูลลูกค้า)</p>
        </div>
        @endif

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

                            // $roomPrice = $orderRoom->room_type-> ?? 0;
                            $duration = @$orderRoom->course->name;
                        @endphp
                        <tr>
                            <td>ค่าบริการห้อง ({{ $orderRoom->room->name }}) - {{ $duration }} นาที</td>
                            <td class="text-end">{{ number_format($room_course_price, 2) }}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ number_format($room_course_price, 2) }}</td>
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
                        <tr>
                            <td>ส่วนลด</td>
                            <td class="text-end">{{ number_format($orderRoom->discount, 2) }}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ number_format($orderRoom->discount, 2) }}</td>
                        </tr>
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
        {{-- <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <label for="status" class="form-label">เปลี่ยนสถานะ</label>
            <select id="orderStatusSelect" class="form-select" data-id="{{ $orderRoom->id }}" data-current="{{ $orderRoom->ref_status_id }}">
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}"
                        {{ $orderRoom->ref_status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div> --}}
        @if ($orderRoom->ref_status_id == 2 || ($orderRoom->ref_status_id != 3 && ($orderRoom->can_manage ?? false)))

        <div class="bg-white p-3 rounded-3 shadow-sm">
            <div align="center">
                <button
                    type="button"
                    id="btn-finish-service"
                    class="btn btn-warning btn-lg fw-bold"
                    onclick="finishService()"
                    {{ $orderRoom->id }}
                >
                    <i class="ti ti-file-description me-2"></i>
                    Check-Out
                </button>
            </div>
            {{-- <label for="payment_method_select" class="form-label">วิธีการชำระเงิน</label>
            <form id="paymentMethodForm" action="#" method="post" onsubmit="return false;">
                <select class="form-select mt-1" id="payment_method_select" name="payment_method" data-id="{{ $orderRoom->id }}" @if($orderRoom->payment_method) disabled @endif>
                    <option value="">-- เลือกวิธีการชำระเงิน --</option>
                    <option value="cash" {{ $orderRoom->payment_method == 'cash' ? 'selected' : '' }}>เงินสด (Cash)</option>
                    <option value="โอน/สแกน QR Code (PromptPay)" {{ $orderRoom->payment_method == 'โอน/สแกน QR Code (PromptPay)' ? 'selected' : '' }}>โอน/สแกน QR Code (PromptPay)</option>
                    <option value="บัตรเครดิต/เดบิต (Credit/Debit Card)" {{ $orderRoom->payment_method == 'บัตรเครดิต/เดบิต (Credit/Debit Card)' ? 'selected' : '' }}>บัตรเครดิต/เดบิต (Credit/Debit Card)</option>
                    <option value="WeChat Pay" {{ $orderRoom->payment_method == 'WeChat Pay' ? 'selected' : '' }}>WeChat Pay</option>
                    <option value="Alipay" {{ $orderRoom->payment_method == 'Alipay' ? 'selected' : '' }}>Alipay</option>
                    <option value="TrueMoney Wallet / LINE Pay (E-Wallet)" {{ $orderRoom->payment_method == 'TrueMoney Wallet / LINE Pay (E-Wallet)' ? 'selected' : '' }}>TrueMoney Wallet / LINE Pay (E-Wallet)</option>
                </select>
            </form> --}}
        </div>
        @elseif (!($orderRoom->can_manage ?? false))
        <div class="bg-white p-3 rounded-3 shadow-sm text-center text-muted">
            รายการนี้ดูและพิมพ์ใบเสร็จได้เท่านั้น
        </div>
        @endif
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

    function finishService() {
        fetch(`/admin/order-rooms/{{ $orderRoom->id }}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                status_id: 3
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'ไม่สามารถเปลี่ยนสถานะได้');
            }
        });
    }
</script>
