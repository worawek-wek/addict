<div class="modal-content border-0 rounded-3">
    <div class="modal-header border-0" style="background-color:#4cc9f0; color:white;">
        <h5 class="modal-title d-flex align-items-center">
            <i class="ti ti-file-description me-2"></i> รายละเอียดการจองห้อง
            <span class="badge ms-3 {{ $orderRoom->badge_class ?? 'bg-secondary' }}">
                {{ $orderRoom->status->name ?? 'ไม่ระบุ' }}
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
                <div class="col-md-6"><strong>ลูกค้า:</strong> {{ $orderRoom->customer->name ?? '-' }}</div>
                <div class="col-md-6"><strong>เบอร์โทร:</strong> {{ $orderRoom->customer->phone ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>พนักงานนวด:</strong> {{ $orderRoom->user->name ?? '-' }}</div>
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

        {{-- Dropdown เปลี่ยนสถานะ --}}
        <div class="bg-white p-3 rounded-3 shadow-sm">
            <label for="status" class="form-label">เปลี่ยนสถานะ</label>
            <select id="orderStatusSelect" class="form-select" data-id="{{ $orderRoom->id }}">
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

        Swal.fire({
            title: 'ยืนยันการเปลี่ยนสถานะ?',
            text: "คุณแน่ใจหรือไม่ที่จะเปลี่ยนสถานะนี้",
            icon: 'warning',
            showCancelButton: true,
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
                        }
                    });
            } else {
                // กดยกเลิก -> reset ค่า dropdown กลับไป
                selectEl.value = selectEl.getAttribute('data-current');
            }
        });
    });
</script>
