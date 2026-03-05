<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <h5 class="modal-title" id="exampleModalLabel1">&nbsp;นำเข้านำเข้า</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <form id="update_stock" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="row g-3 p-4">
                <div class="col-sm-6">
                    <label for="" class="form-label">นำเข้า</label>
                    <select name="ref_drink_id" id="select2Position1" class="form-select" data-allow-clear="true">
                        @foreach ($drink as $pro)
                            <option value="{{$pro->id}}" @if($pro->id == $stock->ref_drink_id) selected @endif>{{ $pro->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="" class="form-label">ราคาต้นทุน</label><span class="text-danger"> *</span>
                    <input name="cost_price" type="number" step="0.01" class="form-control" placeholder="ราคาต้นทุน" value="{{ $stock->cost_price }}" required />
                </div>
                <div class="col-sm-6">
                    <label for="" class="form-label">รายการ</label><span class="text-danger"> *</span>
                    <input name="label" type="text" class="form-control" placeholder="รายการ" value="{{ $stock->label }}" required />
                </div>
                <div class="col-sm-6">
                    <label for="" class="form-label">จำนวนรับเข้า</label><span class="text-danger"> *</span>
                    <input name="quantity" type="text" class="form-control" placeholder="จำนวนรับเข้า" value="{{ $stock->quantity }}" required />
                </div>
                <div class="col-sm-12">
                    <label for="" class="form-label">หมายเหตุ</label>
                    <textarea name="remark" class="form-control"> {{ $stock->remark }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer rounded-0 justify-content-center">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
            <button type="submit" class="btn btn-main">บันทึก</button>
        </div>
    </form>
</div>
<script>
        $('#update_stock').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ

            if (!this.checkValidity()) {
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }

            var formData = new FormData(this);

            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการแก้ไขหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                didOpen: () => {
                    Swal.getConfirmButton().focus();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{$page_url}}/{{ $stock->id }}',
                        type: 'POST',
                        data: formData,
                        contentType: false, // ✅ ต้องมี
                        processData: false, // ✅ ต้องมี
                        success: function(response) {
                            if (response == true) {
                                $('#update_stock')[0].reset();
                                Swal.fire('แก้ไขเรียบร้อยแล้ว', '', 'success');
                                $('#insurance').modal('hide');
                                loadData(page);
                            }
                        },
                        error: function(error) {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                            console.error('เกิดข้อผิดพลาด:', error);
                        }
                    });
                }
            });
        });
</script>