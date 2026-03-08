
<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <span class="modal-title">
            <span class="h5" style="color: white;">&nbsp;แก้ไข การตั้งค่าคอม (นวด+สินค้า)&nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body pb-5" style="padding: 1em 3em;">

        <div class="col-md-12" style="padding-right: unset !important;">

            <div class="card shadow-none bg-transparent border mb-3">
                <div class="card-body">
                    <div class="nav-align-top mb-4">
                        <div class="tab-content" style="box-shadow: unset;padding:0px">
                            <form id="edit_commission" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">สาขา</label>
                                        @if(auth()->user()->ref_position_id == 0)
                                        <select name="ref_branch_id" class="form-control select2-branch"
                                            required>
                                            <option value="">เลือกสาขา</option>
                                            @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <input type="text" class="form-control"
                                            value="{{ auth()->user()->branch->name ?? '-' }}" readonly>
                                        <input type="hidden" name="ref_branch_id"
                                            value="{{ auth()->user()->ref_branch_id }}">
                                        @endif
                                    </div>
                                    <div></div>
                                    <div class="col-md-6">
                                        <label class="form-label">ยอดขายขั้นต่ำ</label>
                                        <input type="number" step="0.01" name="min_sales_amount" value="{{ $tiers->min_sales_amount }}"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ยอดขายสูงสุด</label>
                                        <input type="number" step="0.01" name="max_sales_amount" value="{{ $tiers->max_sales_amount }}"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-3 d-flex justify-content-end align-items-center">
                                        <div class="form-check">
                                            <input name="commission_by" class="form-check-input me-2 commission_by" type="checkbox" value="1" id="view_commission_by" @if($tiers->commission_by == 1) checked @endif />
                                            <label class="form-check-label" for="view_commission_by" style="font-size: small;">
                                                อัตราคอมมิชชั่น (%)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="0.01" name="commission_rate" class="form-control" value="{{ $tiers->commission_rate }}">
                                    </div>
                                    <div class="col-md-3 d-flex justify-content-end align-items-center">
                                        <div class="form-check">
                                            <input name="commission_by" class="form-check-input me-2 commission_by" type="checkbox" value="2" id="view_commission_by_2" @if($tiers->commission_by == 2) checked @endif />
                                            <label class="form-check-label" for="view_commission_by_2" style="font-size: small;">
                                                อัตราคอมมิชชั่น (บาท)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="1" name="commission_price" class="form-control" value="{{ $tiers->commission_price }}">
                                    </div>
                                </div>
                                <script>
                                    document.querySelectorAll('.commission_by').forEach(function(checkbox) {
                                        checkbox.addEventListener('change', function() {
                                            document.querySelectorAll('.commission_by').forEach(function(item) {
                                                if (item !== checkbox) item.checked = false;
                                            });
                                        });
                                    });
                                </script>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-main ms-2">บันทึก</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#edit_commission').on('submit', function(event) {
        event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
        if (!this.checkValidity()) {
            // ถ้าฟอร์มไม่ถูกต้อง
            this.reportValidity();
            return console.log('ฟอร์มไม่ถูกต้อง');
        }
        // return alert(123);

        var formData = new FormData(this);
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการ แก้ไข ค่าคอม หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            showDenyButton: false,
            didOpen: () => {
                // โฟกัสที่ปุ่ม confirm
                Swal.getConfirmButton().focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/sales-commission-tier/{{ $tiers->id }}', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                    type: 'POST',
                    data: formData,
                    contentType: false, // ✅ ต้องมี
                    processData: false, // ✅ ต้องมี
                    success: function(response) {
                        if (response == true) {
                            Swal.fire('แก้ไขค่าคอมเรียบร้อยแล้ว', '', 'success').then((result) => {
                                location.reload();
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            } else if (result.isDismissed) {
                // Swal.fire('ยกเลิกการดำเนินการ', '', 'info');
            }
        });
    });
</script>