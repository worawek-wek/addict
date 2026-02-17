<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<style>
    .select2-container {
        z-index: 9999; /* dropdown อยู่บนสุด */
    }
    .swal2-container {
        z-index: 9999 !important; /* SweetAlert อยู่บนสุด */
    }
</style>

<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <span class="modal-title">
            <span class="h5" style="color: white;">&nbsp;รายละเอียด ห้อง&nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body pb-5" style="padding: 1em 3em;">

        <div class="col-md-12">

            <div class="card shadow-none bg-transparent border mb-3">
                <div class="card-body">
                    <div class="nav-align-top mb-4">
                        <div class="tab-content" style="box-shadow: unset;padding:0px">

                            {{-- <div class="tab-pane fade" id="navs-pills-top-edit" role="tabpanel"> --}}
                                <div class="col-sm-12 text-start">
                                    <h5 class="border-bottom pb-3 text-warning">
                                        <i class="ti ti-pencil"></i> แก้ไข
                                    </h5>
                                </div>
                                <form id="edit_course" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3 p-4">
                                        <div class="col-sm-12">
                                            <label class="form-label">สาขา *</label><br>
                                            @foreach ($branch as $bra)
                                                <input class="form-check-input" type="radio" name="ref_branch_id"
                                                       value="{{ $bra->id }}"
                                                       {{ $course->ref_branch_id == $bra->id ? 'checked' : '' }}>
                                                <label class="form-check-label me-4">{{ $bra->name }}</label>
                                            @endforeach
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">ชื่อคอร์ส <span class="text-danger">*</span></label>
                                            <input name="name" type="text" class="form-control" value="{{ $course->name }}" placeholder="ชื่อคอร์ส" required />
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label class="form-label">จำนวณ นาที <span class="text-danger">*</span></label>
                                            <input name="minute" type="text" class="form-control" value="{{ $course->minute }}" placeholder="จำนวณ นาที" required />
                                        </div>

                                        <div class="col-sm-12">
                                            <label class="form-label">หมายเหตุ</label>
                                            <textarea name="remark" class="form-control">{{ $course->remark }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer rounded-0 justify-content-center">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button type="submit" class="btn btn-main">บันทึก</button>
                                    </div>
                                </form>
                            {{-- </div> --}}

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$('#edit_course').on('submit', function(event) {
    event.preventDefault();

    if (!this.checkValidity()) {
        this.reportValidity();
        return;
    }

    var formData = new FormData(this);
    Swal.fire({
        title: 'ยืนยันการดำเนินการ?',
        text: 'คุณต้องการแก้ไขห้องนี้หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        allowEnterKey: true,

        didOpen: () => {
            // 🔥 ดัก Enter แบบ force
            document.addEventListener('keydown', handleSwalEnter);
        },

        willClose: () => {
            // 🧹 ล้าง event ตอนปิด
            document.removeEventListener('keydown', handleSwalEnter);
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{$page_url}}/{{$course->id}}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == true) {
                        Swal.fire('แก้ไขห้องเรียบร้อยแล้ว', '', 'success');
                        var modalEl = document.getElementById('insurance');
                        var modalInstance = bootstrap.Modal.getInstance(modalEl); // <-- ดึง instance ที่เปิดอยู่
                        if (modalInstance) {
                            modalInstance.hide(); // <-- ซ่อน modal ที่เปิดอยู่จริง
                        }
                        loadData(page);
                        // view('{{$course->id}}');
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
